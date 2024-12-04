@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5">
            <h5>
                {{ $error }}
            </h5>
        </div>
    @endforeach
@endif
@if (session()->get('succes'))
    <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">
        <h5>
            {{ session()->get('succes') }}
        </h5>
    </div>
@endif
