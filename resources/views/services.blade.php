<x-app-layout>

	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Services') }}
		</h2>
	</x-slot>

	<div class="py-12 overflow-y-auto">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<livewire:user-services/>
		</div>
	</div>

</x-app-layout>
