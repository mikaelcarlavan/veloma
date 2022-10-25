<?php

$isLoggedIn = $user->id > 0;

?>
<div class="relative bg-white shadow"  x-data="{mobileMenuOpened: false}">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex items-center justify-between py-6 md:justify-start md:space-x-10">
            <div class="flex justify-start lg:w-0 lg:flex-1">
                <a href="<?php echo $url; ?>">
                    <span class="sr-only"><?php echo $appli; ?></span>
                    <img class="h-8 w-auto sm:h-10" src="<?php echo $favicon; ?>" alt="">
                </a>
            </div>
            <div class="-my-2 -mr-2 md:hidden">
                <button @click="mobileMenuOpened = true" type="button" class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500" aria-expanded="false">
                    <span class="sr-only"><?php echo $langs->trans('VelomaOpenMenu'); ?></span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <nav class="hidden space-x-10 md:flex">
                <?php if ($isLoggedIn): ?>
                <a href="<?php echo dol_buildpath('/veloma/public/rentals.php', 1); ?>" class="text-base font-medium text-gray-500 hover:text-gray-900"><?php echo $langs->trans('VelomaMyRentals'); ?></a>
                <a href="<?php echo dol_buildpath('/veloma/public/bookings.php', 1); ?>" class="text-base font-medium text-gray-500 hover:text-gray-900"><?php echo $langs->trans('VelomaMyBookings'); ?></a>
                <?php endif; ?>
            </nav>
            <div class="hidden items-center justify-end md:flex md:flex-1 lg:w-0">
                <?php if ($isLoggedIn): ?>
                    <button @click="accountModalOpened = true" type="button" class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"><?php echo $langs->trans('VelomaMyAccount'); ?></button>
                    <a href="<?php echo dol_buildpath('/veloma/public/logout.php', 1); ?>" class="ml-8 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700"><?php echo $langs->trans('VelomaLogOut'); ?></a>
                <?php else: ?>
                    <button @click="loginModalOpened = true" type="button" class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900"><?php echo $langs->trans('VelomaSignIn'); ?></button>
                    <button @click="registerModalOpened = true" type="button" class="ml-8 inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700"><?php echo $langs->trans('VelomaSignUp'); ?></button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpened"
         x-transition:enter="duration-200 ease-out"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="duration-100 ease-in"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute inset-x-0 top-0 z-10 origin-top-right transform p-2 transition md:hidden">
        <div class="divide-y-2 divide-gray-50 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
            <div class="px-5 pt-5 pb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <img class="h-8 w-auto" src="<?php echo $favicon; ?>" alt="<?php echo $appli; ?>">
                    </div>
                    <div class="-mr-2">
                        <button @click="mobileMenuOpened = false" type="button" class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500">
                            <span class="sr-only"><?php echo $langs->trans('VelomaCloseMenu'); ?></span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="space-y-6 py-6 px-5">
                <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                    <?php if ($isLoggedIn): ?>
                    <a href="<?php echo dol_buildpath('/veloma/public/rentals.php', 1); ?>" class="text-base font-medium text-gray-900 hover:text-gray-700"><?php echo $langs->trans('VelomaMyRentals'); ?></a>
                    <a href="<?php echo dol_buildpath('/veloma/public/bookings.php', 1); ?>" class="text-base font-medium text-gray-900 hover:text-gray-700"><?php echo $langs->trans('VelomaMyBookings'); ?></a>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?php echo dol_buildpath('/veloma/public/logout.php', 1); ?>" class="flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700"><?php echo $langs->trans('VelomaLogOut'); ?></a>
                    <?php else: ?>
                        <button @click="registerModalOpened = true" type="button" class="flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700"><?php echo $langs->trans('VelomaSignUp'); ?></button>
                        <p class="mt-6 text-center text-base font-medium text-gray-500">
                            <?php echo $langs->trans('VelomaAlreadyHaveAnAccount'); ?>
                            <button @click="loginModalOpened = true" type="button"  class="text-green-600 hover:text-green-500"><?php echo $langs->trans('VelomaSignIn'); ?></button>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
