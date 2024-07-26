<x-filament-panels::page>
   <form wire:submit="save" method="post" class="text-center"> 
      {{ $this->form }}
      <button type="submit" class="bg-blue-500 hover:bg-blue-700  text-white font-bold py-2 mt-2 px-4 rounded focus:outline-none focus:shadow-outline ">Save</button>
   </form>
</x-filament-panels::page>
