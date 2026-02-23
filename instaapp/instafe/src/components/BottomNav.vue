<template>
  <nav class="sticky bottom-0 z-50 flex items-center justify-around h-14 bg-white border-t border-gray-200 pb-[env(safe-area-inset-bottom)]">
    <RouterLink
      to="/"
      class="flex flex-col items-center gap-0.5 text-gray-500 no-underline"
      active-class="!text-primary"
      :exact="true"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z" />
      </svg>
      <span class="text-xs">Home</span>
    </RouterLink>
    <RouterLink
      to="/profile"
      class="flex flex-col items-center gap-0.5 text-gray-500 no-underline"
      active-class="!text-primary"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      <span class="text-xs">Profile</span>
    </RouterLink>
  </nav>
</template>
