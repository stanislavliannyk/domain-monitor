{{--
    Shared domain form partial.
    Props: $domain (optional, for edit), $action (string), $method (string)
--}}

@php $isEdit = isset($domain) && $domain->exists; @endphp

<form method="POST" action="{{ $action }}" class="space-y-6" x-data="domainForm()">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Basic info --}}
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Display name <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" required maxlength="100"
                   value="{{ old('name', $domain->name ?? '') }}"
                   placeholder="My Website"
                   class="mt-1 block w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror
                          px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="url" class="block text-sm font-medium text-gray-700">URL <span class="text-red-500">*</span></label>
            <input type="url" id="url" name="url" required maxlength="2048"
                   value="{{ old('url', $domain->url ?? '') }}"
                   placeholder="https://example.com"
                   class="mt-1 block w-full rounded-lg border @error('url') border-red-400 @else border-gray-300 @enderror
                          px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            @error('url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Check settings --}}
    <div class="rounded-lg border border-gray-200 p-5 space-y-5">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Check Settings</h3>

        <div class="grid gap-5 sm:grid-cols-3">
            {{-- Interval --}}
            <div>
                <label for="check_interval" class="block text-sm font-medium text-gray-700">
                    Interval (minutes)
                </label>
                <select id="check_interval" name="check_interval"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                               shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @foreach ([1, 5, 10, 15, 30, 60] as $min)
                        <option value="{{ $min }}"
                            {{ old('check_interval', $domain->check_interval ?? 5) == $min ? 'selected' : '' }}>
                            {{ $min }} min{{ $min > 1 ? 's' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('check_interval') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Timeout --}}
            <div>
                <label for="request_timeout" class="block text-sm font-medium text-gray-700">
                    Timeout (seconds)
                </label>
                <select id="request_timeout" name="request_timeout"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                               shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @foreach ([5, 10, 15, 20, 30, 60] as $sec)
                        <option value="{{ $sec }}"
                            {{ old('request_timeout', $domain->request_timeout ?? 10) == $sec ? 'selected' : '' }}>
                            {{ $sec }}s
                        </option>
                    @endforeach
                </select>
                @error('request_timeout') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Method --}}
            <div>
                <label for="check_method" class="block text-sm font-medium text-gray-700">
                    HTTP Method
                </label>
                <select id="check_method" name="check_method"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                               shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @foreach (['HEAD', 'GET'] as $m)
                        <option value="{{ $m }}"
                            {{ old('check_method', $domain->check_method ?? 'HEAD') === $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">HEAD is faster and uses less bandwidth.</p>
            </div>
        </div>

        {{-- Active toggle --}}
        <div class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $domain->is_active ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="is_active" class="text-sm text-gray-700">Enable monitoring for this domain</label>
        </div>
    </div>

    {{-- Notifications (bonus) --}}
    <div class="rounded-lg border border-gray-200 p-5 space-y-4" x-data="{ notifyEnabled: {{ old('notify_on_failure', $domain->notify_on_failure ?? false) ? 'true' : 'false' }} }">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Notifications</h3>

        <div class="flex items-center gap-3">
            <input type="hidden" name="notify_on_failure" value="0">
            <input type="checkbox" id="notify_on_failure" name="notify_on_failure" value="1"
                   x-model="notifyEnabled"
                   {{ old('notify_on_failure', $domain->notify_on_failure ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="notify_on_failure" class="text-sm text-gray-700">
                Send email notification when domain goes down
            </label>
        </div>

        <div x-show="notifyEnabled" x-transition>
            <label for="notification_email" class="block text-sm font-medium text-gray-700">
                Notification email
            </label>
            <input type="email" id="notification_email" name="notification_email"
                   value="{{ old('notification_email', $domain->notification_email ?? '') }}"
                   placeholder="ops@example.com"
                   class="mt-1 block w-full rounded-lg border @error('notification_email') border-red-400 @else border-gray-300 @enderror
                          px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
            <p class="mt-1 text-xs text-gray-400">Leave blank to use your account email.</p>
            @error('notification_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow
                       hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            {{ $isEdit ? 'Save Changes' : 'Add Domain' }}
        </button>
        <a href="{{ $isEdit ? route('domains.show', $domain) : route('domains.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
    </div>
</form>

<script>
function domainForm() {
    return {};
}
</script>
