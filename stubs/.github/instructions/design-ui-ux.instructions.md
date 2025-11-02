---
applyTo: '**'
---

# Temula UI/UX Design System

This document defines Temula's visual design language, component patterns, accessibility standards, and interaction patterns. It serves as the canonical reference for all UI/UX decisions across the platform.

## Design Philosophy

**Principle: Elegant Simplicity in Service of Events**

Temula's design embodies three core values:

1. **Clarity**: Information hierarchy is clear; users always know their next action.
2. **Confidence**: The interface inspires trust through professional, modern aesthetics.
3. **Delight**: Micro-interactions and personality (Mora) create moments of joy without distraction.

---

## Color Palette

### Primary Brand Colors

The brand palette consists of two complementary color systems working in harmony:

#### Brand Blue (Primary)
Used for primary actions, navigation, and trusted brand presence.

```
brand-50:   #eef5ff  (lightest, backgrounds)
brand-100:  #dfeaff
brand-200:  #bfd4ff
brand-300:  #9ebdff
brand-400:  #7ea7ff
brand-500:  #5e91ff  (base brand blue)
brand-600:  #3c73e6  ⭐ PRIMARY CTA & HOVER STATE
brand-700:  #2e57b4  (deep interactive)
brand-800:  #223d82  (dark interactive)
brand-900:  #172652  (deepest, accents)
```

**Usage:**
- CTAs (buttons, links): brand-600 (regular) → brand-700 (hover)
- Backgrounds: brand-50 to brand-200
- Gradients: brand-50 → transparent for hero sections
- Badge/tags: brand-200 bg with brand-700 text

#### Accent Orange (Secondary/Playful)
Used for highlights, personality, and secondary calls-to-action. Represents Mora's energy.

```
accent-50:   #fff3f1  (lightest)
accent-100:  #ffe6e1
accent-200:  #ffcfc6
accent-300:  #ffb0a0
accent-400:  #ff8a73
accent-500:  #ff6a4f  ⭐ ACCENT BASE
accent-600:  #e5543c  (hover)
accent-700:  #b8412f  (deep)
accent-800:  #8a3023  (dark)
accent-900:  #5d2017  (deepest)
```

**Usage:**
- Mora illustrations and mascot
- Success states and positive feedback
- Highlight sections and special features
- Call-to-action gradients (brand-600 → accent-500)

### Neutral Colors

Anchored on **Slate** (Tailwind's slate scale):

```
Light Mode:
- slate-50:  #f8fafc  (Lightest backgrounds)
- slate-100: #f1f5f9  (Secondary backgrounds)
- slate-200: #e2e8f0  (Borders, dividers)
- slate-300: #cbd5e1
- slate-400: #94a3b8
- slate-500: #64748b  (Muted text)
- slate-600: #475569  (Secondary text)
- slate-700: #334155  (Primary text for secondary)
- slate-800: #1e293b  (Primary text)
- slate-900: #0f172a  (Deepest text/headings)

Dark Mode:
- slate-950: #020617  (Background)
- slate-900: #0f172a  (Surfaces)
- slate-800: #1e293b  (Cards/panels)
- slate-700: #334155  (Dividers)
- ... (standard Tailwind dark slate)
```

### Semantic Colors

- **Success**: `green-500` (#22c55e) — Confirmations, completed states
- **Warning**: `amber-500` (#f59e0b) — Cautions, alerts
- **Error**: `red-500` (#ef4444) — Destructive actions, validation errors
- **Info**: `blue-500` (brand-500) — Informational messages

---

## Typography

### Font Family

**Primary**: Inter (variable weight)
- Fallbacks: system-ui, -apple-system, BlinkMacSystemFont, sans-serif
- **Rationale**: Highly legible, modern, excels at small and large sizes. Excellent for both UI and body copy.

**Loading**: Imported from Google Fonts (preconnect for performance)

### Type Scale & Usage

#### Headings (Page Structure)

| Size     | Class         | Usage                              | Example        |
|----------|---------------|------------------------------------|-----------------|
| 48px     | `text-5xl`    | Hero/page main headline            | "Let's meet..."  |
| 36px     | `text-4xl`    | Section headers                    | "Everything you need..." |
| 30px     | `text-3xl`    | Subsection headers                 | "Simple pricing" |
| 24px     | `text-2xl`    | Card titles, subheaders            |                |
| 20px     | `text-xl`     | Feature titles, modal headers      |                |
| 18px     | `text-lg`     | Body + emphasis                    |                |

**Font Weight**: 600 (semibold) to 800 (extrabold) depending on hierarchy.

#### Body & UI Text

| Size     | Class         | Usage                              | Example        |
|----------|---------------|------------------------------------|-----------------|
| 16px     | `text-base`   | Primary body text, form inputs    | Paragraph copy |
| 14px     | `text-sm`     | Secondary text, helper copy       | Captions, alerts |
| 12px     | `text-xs`     | Tertiary/meta text                | Timestamps     |

**Font Weight**: 400 (regular) for body, 500 (medium) for labels, 600 (semibold) for emphasis.

### Line Height & Spacing

- **Headings**: `leading-tight` (1.25) for text-2xl and above; `leading-snug` (1.375) for smaller headings
- **Body**: `leading-relaxed` (1.625) for long-form; `leading-normal` (1.5) for UI copy
- **Code/mono**: `leading-normal` (1.5)

### Text Colors

- **Primary text** (headings, labels): `text-slate-900` (light) / `dark:text-slate-100`
- **Secondary text**: `text-slate-600` (light) / `dark:text-slate-300`
- **Tertiary/muted**: `text-slate-500` (light) / `dark:text-slate-400`
- **On brand**: `text-white` (on brand-600, brand-700)
- **On accent**: `text-white` (on accent-500, accent-600)

---

## Component Patterns

### Buttons

#### Primary CTA
```
Base: bg-brand-600 text-white font-semibold rounded-lg px-5 py-3
Hover: bg-brand-700
Active: bg-brand-800
Focus: ring-2 ring-brand-500 ring-offset-2 (light) / ring-offset-slate-950 (dark)
Disabled: opacity-50 cursor-not-allowed
```

**Examples:**
- "Get started free"
- "Create an event"
- "Start Pro"

#### Secondary Button
```
Base: border border-slate-300 text-slate-700 font-semibold rounded-lg px-5 py-3
Dark: dark:border-slate-700 dark:text-slate-300
Hover: bg-slate-50 dark:bg-slate-800
```

**Examples:**
- "See how it works"
- "Talk to sales"
- "Learn more"

#### Tertiary / Ghost Button
```
Base: text-brand-600 font-semibold hover:underline
Dark: dark:text-brand-400
```

**Examples:**
- Links, breadcrumbs, inline actions

#### Small/Compact Button
```
Base: px-3 py-2 text-sm font-semibold rounded-lg
(apply same color rules as above)
```

**Examples:**
- Inline actions, compact UI spaces

### Cards & Containers

#### Standard Card
```
Base: rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-100
Dark: dark:bg-slate-900 dark:ring-slate-800
```

**Shadow Definition:**
```
shadow-soft: 0 10px 30px -10px rgba(23,38,82,.25)
```

**Hierarchy:**
- **Elevated/Featured**: Add `border-2 border-brand-300 shadow-soft` (e.g., Pro pricing card)
- **Flat**: Remove shadow, keep ring (`ring-1 ring-slate-200 dark:ring-slate-800`)

#### Form Input / Field
```
Base: rounded-lg border border-slate-300 px-4 py-2 text-slate-900
Focus: ring-2 ring-brand-500 border-brand-500
Dark: dark:bg-slate-900 dark:border-slate-700 dark:text-white
Error: ring-2 ring-red-500 border-red-500
```

### Badges & Tags

#### Default Badge
```
Base: rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700
Dark: dark:bg-slate-800 dark:text-slate-300
```

#### Brand Badge
```
Base: rounded-full border border-brand-200 bg-white px-3 py-1 text-xs font-semibold text-brand-700 shadow-sm
Dark: dark:bg-slate-900 dark:border-slate-700
```

#### Status Badge (Success)
```
Base: rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700
```

### Navigation & Menus

#### Primary Navigation (Sticky Header)
```
Base: sticky top-0 z-50 bg-white/70 backdrop-blur border-b border-slate-200
Dark: dark:bg-slate-950/70 dark:border-slate-700
Transition: transition-colors duration-300
```

**Brand Logo:**
- 28×28px icon + text (hidden on mobile, shown on tablet+)
- `.drop-shadow-sm` for icon depth

**Nav Links:**
```
Base: text-sm font-medium text-slate-700 hover:text-brand-600
Dark: dark:text-slate-300 dark:hover:text-brand-400
Transition: transition-colors duration-200
```

#### Breadcrumb / Step Indicator
```
Base: flex gap-2 items-center
Separator: /
Current: font-semibold text-slate-900
Previous: text-slate-500 cursor-pointer
```

### Sections & Backgrounds

#### Hero Section
```
Background: relative overflow-hidden with gradient overlay
Gradient: bg-gradient-to-b from-brand-50 to-transparent dark:from-slate-900
Max-width: mx-auto max-w-7xl
Padding: px-4 sm:px-6 lg:px-8 py-20 lg:py-28
```

#### Feature Section
```
Background: bg-gradient-to-b from-transparent to-brand-50/60 dark:to-slate-900/60
Padding: py-20
Layout: Grid (2-col on lg, 1-col on sm)
Gap: gap-12 (items-start)
```

#### Form/Modal Sections
```
Base: rounded-xl bg-white p-4 dark:bg-slate-900
Border: border border-slate-200 dark:border-slate-800
Gap (stacked): space-y-4
```

### Modals & Dialogs

```
Backdrop: fixed inset-0 z-50 grid place-items-center bg-black/70 p-6
Container: w-full max-w-3xl rounded-xl bg-white p-4 shadow-xl dark:bg-slate-900
Close button: text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors
```

### List & Feature Lists

#### Icon + Text Pattern (Features)
```
Container: flex gap-3
Icon: w-5 h-5 mt-1 flex-shrink-0 text-brand-600
Title: font-semibold text-slate-900 dark:text-white
Description: text-slate-600 dark:text-slate-300
```

**Examples:**
- Feature lists (checkmarks + text)
- Team member roles

#### Definition List (FAQ)
```
Container: space-y-3
Item: rounded-xl border border-slate-200 p-4 dark:border-slate-800
Question: font-semibold cursor-pointer
Answer: mt-2 text-slate-600 dark:text-slate-300
Toggle indicator: text-right (+ / –)
```

---

## Spacing & Layout

### Padding Tokens

- **Tight**: px-2, py-2
- **Compact**: px-3 py-2 (text-sm)
- **Default**: px-4 py-3 / px-5 py-3
- **Relaxed**: px-6 py-4
- **Generous**: px-8 py-6

### Gap & Margins

| Use Case | Value | Class |
|----------|-------|-------|
| Icon + text | 8px | `gap-2` |
| Inline buttons | 12px | `gap-3` |
| Card columns | 24px | `gap-6` |
| Section columns | 48px | `gap-12` |
| Stacked sections | 80px | `space-y-20` |

### Container Sizes

| Breakpoint | Max Width | Class       |
|------------|-----------|-------------|
| Default   | 64rem     | `max-w-4xl` |
| Full page | 80rem     | `max-w-7xl` |
| Centered  | 42rem     | `max-w-2xl` |
| Card/panel | 28rem | `max-w-sm` |

---

## Responsive Breakpoints

Temula uses Tailwind's standard breakpoints with mobile-first approach:

```
sm: 640px   (tablet portrait)
md: 768px   (tablet landscape)
lg: 1024px  (desktop)
xl: 1280px  (large desktop)
2xl: 1536px (extra large)
```

**Default Strategy:**
- Mobile optimized first (no prefix)
- Tablet refinements (`sm:` / `md:`)
- Desktop enhancements (`lg:` / `xl:`)

**Example:**
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- 1 col on mobile, 2 on tablet, 3 on desktop -->
</div>
```

---

## Interaction & Animation

### Transitions

- **Standard UI**: `transition-colors duration-200`
- **Emphasis**: `transition-transform duration-300`
- **Opacity**: `transition-opacity duration-300`
- **All**: `transition-all duration-300` (use sparingly)

### Hover States

- **Buttons**: `hover:bg-{color}-700` + shadow enhancement
- **Links**: `hover:text-brand-600` or `hover:underline`
- **Cards**: `hover:shadow-lg hover:ring-brand-300` (on hover-lift patterns)
- **Icons**: `hover:text-{color}` + `transition-colors`

### Focus States

- **Keyboard nav**: `focus:ring-2 ring-brand-500` + `focus:outline-none`
- **Buttons**: `focus:ring-2 ring-brand-500 ring-offset-2`
- **Dark mode**: `focus:ring-offset-slate-950`

### Loading & Skeleton States

- **Loading spinner**: Use Alpine/Livewire `wire:loading`
- **Skeleton**: `bg-slate-200 dark:bg-slate-700 animate-pulse`
- **Disabled state**: `opacity-50 cursor-not-allowed pointer-events-none`

### Scroll Behavior

```css
html {
  scroll-behavior: smooth;
}
```

---

## Dark Mode

### Implementation

- **Triggered by**: `class="dark"` on `<html>` element (via Alpine `x-data`)
- **Storage**: `localStorage.getItem('theme')`
- **Initialization**: Run before Alpine hydration to prevent flash

### Color Adjustments by Component

**General Rule**: Use `dark:` prefix for 80% of components. Exceptions:

- **Always white**: Modal backdrops, white icons
- **Always light**: Primary CTAs should maintain brand-600 in dark mode (high contrast)
- **Inverted**: Secondary buttons → text-slate-300 border-slate-700

### Dark Mode Guidance

```blade
<!-- Good: Clearly adapts -->
<div class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100">

<!-- Good: Brand stays accessible -->
<button class="bg-brand-600 dark:bg-brand-600 hover:bg-brand-700 dark:hover:bg-brand-700">

<!-- ✗ Avoid: Over-inverted -->
<p class="text-white dark:text-slate-900"> <!-- Wrong! -->
```

---

## Accessibility

### WCAG 2.1 Compliance

All UI components must meet **Level AA** minimum standards.

### Color Contrast

- **Text on background**: 4.5:1 for body, 3:1 for large text (18pt+)
- **UI components**: 3:1 minimum for interactive elements
- **Validation**: Use contrast checker for custom colors

**Examples:**
- ✓ brand-600 on white: 7.1:1 (excellent)
- ✓ slate-600 on white: 7:1 (excellent)
- ✓ accent-500 on white: 3.5:1 (good)

### Focus Management

- **Visible focus**: Always show `:focus-visible` ring; never remove it
- **Focus order**: Ensure logical tab order (left-to-right, top-to-bottom)
- **Skip links**: Include "Skip to main content" on every page

### Labels & ARIA

- **Form inputs**: Always pair with `<label>` (not placeholder-only)
- **Icon buttons**: `aria-label="descriptive text"`
- **Modals**: `role="dialog"` + `aria-labelledby`, `aria-describedby`
- **Live regions**: Use `role="status"` for notifications

### Keyboard Navigation

- **Button/link activation**: Enter / Space
- **Focus visible**: Tab through all interactive elements
- **Escape to close**: Modals, dropdowns, overlays

---

## Component Library Reference

### Flux Components (Livewire Flux)

Temula leverages **Livewire Flux** for consistent UI primitives:

```blade
<x-flux::button>Submit</x-flux::button>
<x-flux::card>Content</x-flux::card>
<x-flux::input type="text" placeholder="..." />
<x-flux::icon name="check" />
```

**Guidelines:**
- Prefer Flux components over custom HTML for consistency
- Override via Tailwind utilities when necessary
- Refer to Flux documentation for available components and variants

### Icons

- **Primary icon library**: Lucide Icons (via Blade UI Kit)
- **Usage**: `<x-flux::icon name="icon-name" variant="mini" />`
- **Variants**: mini (16px), default (24px), large (32px)
- **Colors**: Inherit from parent (no hardcoded color on icon unless justified)

**Common Icons:**
- Check, X, ChevronDown, Menu, Search, Settings, Bell, Home, etc.

---

## Use Case Patterns

### CTA Hierarchy (Hero Section)

```blade
<!-- Primary CTA -->
<a class="bg-brand-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-brand-700">
  Get started free
</a>

<!-- Secondary CTA (lower priority) -->
<button class="border border-slate-300 px-5 py-3 rounded-lg font-semibold hover:bg-slate-50">
  ▶ See how it works
</button>
```

### Card with Pricing

```blade
<div class="rounded-2xl border-2 border-brand-300 p-6 shadow-soft">
  <h3>Pro</h3>
  <p>RM79<span>/mo</span></p>
  <ul><!-- features --></ul>
  <button class="w-full bg-brand-600 text-white">Start Pro</button>
</div>
```

### Feature Card (Image + Copy)

```blade
<div class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-100">
  <div class="rounded-xl bg-slate-100 h-40"><!-- image/mock --></div>
  <h4>Feature Title</h4>
  <p>Description</p>
</div>
```

### Form Field with Error

```blade
<div>
  <label for="email" class="block text-sm font-semibold">Email</label>
  <input id="email" type="email"
    class="mt-2 w-full rounded-lg border border-red-500 ring-2 ring-red-500 px-4 py-2"
  />
  <p class="mt-1 text-xs text-red-600">Please enter a valid email</p>
</div>
```

---

## Implementation Checklist

When building a new feature or page:

- [ ] **Typography**: Headings use correct size/weight; body text is 16px base
- [ ] **Colors**: All text meets 4.5:1 contrast; brand colors used consistently
- [ ] **Spacing**: Consistent gaps; padding follows Temula scale
- [ ] **Responsive**: Mobile-first; tested on sm, lg, xl breakpoints
- [ ] **Dark mode**: All `dark:` variants applied; no color flashes on toggle
- [ ] **Focus states**: Keyboard navigation works; visible focus ring present
- [ ] **Icons**: Lucide icons used; size and color intentional
- [ ] **Shadows**: Only `shadow-soft` unless otherwise justified
- [ ] **Buttons**: Primary/secondary/tertiary clearly differentiated
- [ ] **Forms**: Labels paired with inputs; validation states clear
- [ ] **Accessibility**: Color not sole indicator; labels present; ARIA where needed

---

## References

- **Tailwind Config**: `tailwind.config.js` (extended colors, shadows)
- **Livewire Flux**: https://flux.laravel.com
- **Blade Icons**: https://blade-ui-kit.com
- **Lucide Icons**: https://lucide.dev
- **WCAG 2.1**: https://www.w3.org/WAI/WCAG21/quickref/
