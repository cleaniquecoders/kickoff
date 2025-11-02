---
applyTo: '**'
---
# Temula Integrations Instructions

This guide defines Temula's integration architecture, covering both inbound integrations (Temula consuming 3rd-party APIs) and outbound integrations (Temula providing APIs and webhooks for external systems).

## Integration Categories

### Inbound Integrations (Temula consumes 3rd-party APIs)

Temula integrates with external services to enhance core capabilities:

#### Payment Gateways (Priority)
- **Stripe**: Global payments, multi-currency, subscriptions
- **BayarCash**: Malaysian payment gateway, local recurring billing
- **Future**: PayPal, Wise for global payouts, regional gateways per market

Responsibilities:
- Create transactions, handle webhooks (payment confirmed/failed/refunded)
- Store gateway references (provider_ref) for reconciliation
- Implement PCI compliance and secure credential storage (encrypted JSON in config)
- Fallback to platform defaults if org credentials missing/invalid

#### Email Service Providers (Phase 1+)
- **AWS SES**: Primary bulk email and transactional delivery
- **SendGrid**: Optional fallback for deliverability
- Responsibilities: send campaigns, track bounces, handle feedback loops

#### SMS Notifications (Enterprise; Phase 4+)
- **Twilio**: SMS delivery for event reminders and check-in codes
- Responsibilities: rate-limited sending, delivery tracking, opt-out compliance

#### Analytics & Tracking (Optional; Phase 5+)
- **Segment**: Event streaming and analytics aggregation
- **Mixpanel**: Custom event tracking for engagement insights
- Responsibilities: Forward attendee engagement events, ticket sales milestones

#### RFID & Hardware Integration (Enterprise; Phase 4)
- **Zebra/Impinj RFID readers**: Real-time checkpoint scanning
- **BLE beacons**: Indoor location and proximity tracking (future)
- Responsibilities: Ingest RFID reads via webhook or polling, update check-in status real-time

#### Data Import/Export
- **Amazon S3/Wasabi**: Media storage and backups
- **Maatwebsite Excel**: CSV/Excel import/export of participant data
- Responsibilities: Secure file storage, data encryption at rest

### Outbound Integrations (External systems consume Temula APIs/webhooks)

Temula exposes APIs and webhooks for external platforms to extend functionality:

#### REST APIs (Enterprise; Phase 5)
- **Public API endpoints** for core resources:
  - Events: CRUD, search, attendance metrics
  - Registrations & Attendees: bulk queries, exports
  - Tickets: list, check QR validity, issue refunds
  - Check-ins: log attendance, real-time dashboards
  - Webhooks: subscribe/unsubscribe from events
- Authentication: Bearer token (Laravel Sanctum) with scoped abilities
- Rate limiting: Per-plan (Free: none, Starter/Pro: 1000 RPM, Enterprise: custom)
- Versioning: v1, v2 (for backward compatibility)

#### Webhook System (Enterprise; Phase 5)
- **Event subscriptions** (per-organization, per-event or organization-wide):
  - registration.created, registration.cancelled, registration.refunded
  - check_in.completed, attendee.checked_in
  - ticket.issued, ticket.void
  - event.published, event.updated, event.archived
  - payment.succeeded, payment.failed
  - (Future) qa.question_posted, poll.voted, draw.winner_selected
- **Delivery guarantees**:
  - HTTPS POST with JSON payload
  - HMAC-SHA256 signature in `X-Temula-Signature` header for security
  - Retry logic: exponential backoff (1m, 5m, 30m, 2h, 24h) up to 5 attempts
  - Webhook event log for auditing and replay
- **Payload structure**:
  - event_id, event_type, timestamp, data (context-specific)
  - Optional: organization_id, team_id (for scoping)

#### Vendor Integrations (Enterprise; Phase 4+)
- **Exhibitor/Vendor portals**: Lead capture, booth analytics, schedule management
- **Service partners**: Streaming providers (YouTube Live integration), RFID operators, catering
- **Responsibilities**: Provide scoped webhooks (e.g., check-ins only) and APIs for partner systems

#### Analytics & BI Tools (Phase 5+)
- **Tableau/Power BI**: Direct connectors or API endpoints for dashboard building
- **Looker**: Custom dashboards leveraging Temula API
- Responsibilities: Query API for historical data, real-time event dashboards

### Integration Data Flow Patterns

#### Pattern 1: Webhook Trigger → External Action
```
Attendee checks in (via QR scan)
  → Temula check_in webhook fires
  → External system (e.g., streaming platform) updates live attendee count
  → Display refreshes in real-time
```

#### Pattern 2: External API → Temula State Update
```
Admin adds discount via Temula UI
  → Temula queries stripe.com/coupons
  → Creates coupon in Temula if valid
  → Stores stripe_coupon_id for sync
```

#### Pattern 3: Bidirectional Sync
```
Organizer updates event in Temula
  → Webhook fires to external CRM
  → CRM updates attendee record
  → CRM webhook calls Temula API to create follow-up task
```

## Security & Compliance

### Credential Management
- **Platform credentials** (payment gateways, SES): Encrypted in `platform_gateway_configs`
- **Organization credentials** (Enterprise): Encrypted in `payment_gateway_configs`, accessible only to org admins
- **API tokens**: Hashed in `api_tokens`, single-use or scoped by ability
- **Webhook secrets**: Stored securely, rotatable per endpoint
- **Rotation policy**: Quarterly for platform credentials; on-demand for org credentials
- **Audit logging**: All integrations logged via Laravel Auditing; webhook deliveries tracked in `webhook_deliveries`

### Rate Limiting & Abuse Prevention
- **API rate limits**: Per-token basis; reject with 429 (Too Many Requests)
- **Webhook backoff**: Exponential retry; disable endpoint after 5 consecutive failures with admin notification
- **CORS & HTTPS**: All external integrations must use HTTPS; no cross-origin browser requests to APIs
- **Data minimization**: Only expose necessary data in API responses; redact sensitive fields (e.g., payment methods)

### Compliance & Privacy
- **GDPR/PDPA**: Respect data retention policies; provide data export/deletion APIs
- **PCI DSS**: Never store raw card data; delegate to payment providers via tokenization
- **Sandbox mode**: Provide test credentials for payment gateways; separate webhook endpoints for testing
- **Signature verification**: Always validate webhook signatures before processing

## Implementation Guidelines

### Creating a New Inbound Integration

1. **Create a service class** (`app/Services/{Integration}Service.php`):
   - Encapsulate API calls, error handling, credential management
   - Log all interactions for debugging

   ```php
   class StripeService
   {
       public function __construct(private StripeClient $client) {}

       public function createPaymentIntent(array $data): PaymentIntent
       {
           // Validate, call API, handle errors, return normalized result
       }

       public function handleWebhookEvent(array $payload): void
       {
           // Verify signature, update state, trigger events
       }
   }
   ```

2. **Create a job/listener** for async processing (via Laravel Queue):
   - Handle webhooks asynchronously to avoid blocking
   - Retry failed jobs with exponential backoff

3. **Add configuration** (`config/integrations.php`):
   - Store API endpoints, timeouts, retry counts
   - Reference credentials from `.env` or secure vault

4. **Add tests**:
   - Mock external API responses
   - Test error scenarios (timeout, invalid credentials, API changes)
   - Verify state updates after integration events

### Creating a New Outbound Integration (API or Webhook)

1. **Design API endpoints** (`routes/api.php` or `routes/api/*.php`):
   - RESTful pattern: GET /events, POST /events, PATCH /events/{id}, DELETE /events/{id}
   - Use resource controllers for consistency
   - Apply middleware for auth, rate limiting, tenancy scoping

   ```php
   Route::group(['middleware' => 'auth:sanctum', 'throttle:600,1'], function () {
       Route::apiResource('events', EventController::class)->scoped();
       Route::post('events/{event}/check-in', [CheckInController::class, 'store']);
   });
   ```

2. **Create webhook event broadcaster** (`app/Actions/Webhook/BroadcastEvent.php`):
   - Fire `WebhookEvent` whenever state changes (registration.created, etc.)
   - Job queues webhook deliveries with retry logic

3. **Implement webhook dispatcher** (`app/Jobs/DispatchWebhook.php`):
   - Fetch active endpoints for the event/organization
   - Sign payload with HMAC-SHA256
   - POST to endpoint with timeout and retry

4. **Add signature verification** in consuming systems:
   - Compare `X-Temula-Signature` with computed HMAC
   - Reject if mismatch or timestamp expired

5. **Document API** (OpenAPI/Swagger):
   - Generate from code using `laravel-openapi` or similar
   - Maintain docs at `/api/docs` or publish to Postman/Stoplight

6. **Add tests**:
   - Mock external HTTP requests
   - Test webhook delivery and retry logic
   - Verify signature validation

## Webhook Event Catalog (Enterprise; Phase 5+)

| Event | Trigger | Payload | Use Case |
|-------|---------|---------|----------|
| registration.created | New ticket purchase | {registration_id, event_id, attendee_id, total_amount} | CRM sync, confirmation email |
| registration.cancelled | User cancels booking | {registration_id, reason} | Refund processing, seat recovery |
| registration.refunded | Refund issued | {registration_id, amount, reason} | Accounting sync, analytics |
| check_in.completed | Attendee scans QR | {event_id, attendee_id, check_in_time, location} | Real-time dashboards, live count |
| ticket.issued | PDF generated | {ticket_instance_id, attendee_id, qr_code_url} | Email delivery, archive |
| event.published | Event goes live | {event_id, starts_at, visibility} | Announcement, discovery notification |
| event.updated | Organizer edits event | {event_id, changes (diff)} | CRM update, attendee notification |
| payment.succeeded | Transaction confirmed | {transaction_id, amount, gateway, reference} | Revenue recognition, analytics |
| payment.failed | Payment declined | {transaction_id, reason, retry_count} | Error handling, support escalation |

## Testing & Monitoring

### Integration Testing
- **Sandbox mode**: Use provider test credentials for payment/SMS gateways
- **Mock responses**: In unit tests, mock external services with realistic payloads
- **End-to-end**: In staging, test full flows with real provider sandboxes

### Monitoring & Alerting
- **API health checks**: Monitor uptime of critical integrations (Stripe, SES, RFID)
- **Webhook delivery**: Dashboard showing success rate, retry count, failures
- **Error tracking**: Log integration errors (Sentry, Rollbar) for debugging
- **Rate limit alerts**: Notify ops if approaching plan limits

### Audit & Compliance
- **Webhook delivery logs**: Timestamp, payload, response, signature, retry count (in `webhook_deliveries`)
- **Integration audit trail**: Via Laravel Auditing for credential changes, API token creation, feature toggles
- **Data retention**: Archive webhook logs per retention policy (default: 90 days)

## Roadmap & Prioritization

### Phase 1 (MVP)
- ✅ Stripe & BayarCash payment gateways
- ✅ AWS SES for email
- API stubs (placeholder endpoints)

### Phase 2–3 (Growth)
- Webhook event system (registration, check-in events)
- API versioning and documentation
- Team-scoped webhook subscriptions

### Phase 4 (Scale)
- RFID hardware integration
- Vendor portal webhooks
- SMS notifications (Twilio)

### Phase 5 (Enterprise)
- Full REST API with pagination, filtering, advanced queries
- Rate limiting per plan
- API token scopes and management dashboard

### Phase 6+ (Platform)
- Real-time subscriptions (WebSocket or gRPC)
- Analytics connectors (Tableau, Looker)
- Partner marketplace for pre-built integrations

## References

- **Payment gateways**: See `billing-and-subscriptions.instructions.md`
- **Database schema**: See `database.instructions.md` (webhook_endpoints, webhook_deliveries, api_tokens)
- **Security**: Follow Laravel's encryption and credentials management best practices
- **Rate limiting**: Use Laravel's built-in throttle middleware
- **Async jobs**: Use Laravel Horizon for queue monitoring
- **Webhook signature example**: HMAC-SHA256(secret, JSON.stringify(payload))
