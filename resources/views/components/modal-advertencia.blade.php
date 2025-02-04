<div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-20">
    <div class="flex flex-col items-center bg-white p-2 border border-black rounded w-5/6 md:w-1/2 lg:w-1/4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-14 h-14 text-red-600">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
        </svg>
        <h5 class="uppercase font-extrabold text-xl">Atención</h5>
        {{$slot}}
    </div>
</div>
