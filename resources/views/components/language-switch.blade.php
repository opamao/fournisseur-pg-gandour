<form action="{{ route('language.switch') }}" method="POST">
    @csrf
    <select name="language" onchange="this.form.submit()"
        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-8 py-1 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
        <option value="fr" {{ app()->getLocale() === 'fr' ? 'selected' : '' }}>French</option>
        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
    </select>
</form>
