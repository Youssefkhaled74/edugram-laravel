
    <p>{{ $billing->first_name }} {{ $billing->last_name }}</p>
    <p>{{ $billing->address1 }}</p>
    <p>{{ $billing->address2 }}</p>
    <p>{{ $billing->stateDetails->name }},{{ $billing->cityDetails->name }} -
        {{ $billing->zip_code }} </p>
    <p> {{ $billing->countryDetails->name }} </p>
