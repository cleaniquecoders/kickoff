@if (request()->route() && request()->route()->getName() && Breadcrumbs::exists(request()->route()->getName()))
    {{ Breadcrumbs::render() }}
@endif
