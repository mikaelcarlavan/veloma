</div>
</main>

<footer class="bg-white">
    <div class="mx-auto max-w-7xl overflow-hidden py-4 px-4 sm:px-6 lg:px-8">
        <div class="mt-4 flex justify-center space-x-6">
            <a href="#" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Facebook</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
            </a>

            <a href="#" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Instagram</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                </svg>
            </a>

            <a href="#" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Twitter</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                </svg>
            </a>
        </div>
        <p class="mt-4 text-center text-base text-gray-400">&copy; <?php echo date('Y'); ?> <?php echo $conf->global->MAIN_INFO_SOCIETE_NOM; ?>. <?php echo $langs->trans('VelomaAllRightsReserved'); ?></p>
    </div>
</footer>

<div x-cloak x-show="loginModalOpened" class="relative " style="z-index: 1000" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="loginModalOpened"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0  overflow-y-auto" style="z-index: 1000">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="loginModalOpened"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                    <button @click="loginModalOpened = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span class="sr-only"><?php echo $langs->trans('VelomaClose'); ?></span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="login" name="login" action="<?php echo dol_buildpath('/veloma/public/index.php', 1); ?>" method="post">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                    <input type="hidden" name="action" value="login">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="">
                            <div class="mt-3 text-center sm:mt-0 sm:my-4 sm:text-left">
                                <h3 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900" id="modal-title"><?php echo $langs->trans('VelomaSignIn'); ?></h3>
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="login-username" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaPhone'); ?></label>
                                        <div class="mt-1">
                                            <input name="login-username" id="login-username" type="text" value="<?php echo GETPOST('login-username'); ?>" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="login-password" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaSignInPassword'); ?></label>
                                        <div class="mt-1">
                                            <input name="login-password" id="login-password" type="password" autocomplete="current-password" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end">
                                        <div class="text-sm">
                                            <button @click="loginModalOpened = false, passwordModalOpened = true" type="button" class="font-medium text-green-600 hover:text-green-500"><?php echo $langs->trans('VelomaPasswordForgotten'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaLogIn'); ?></button>
                        <button @click="loginModalOpened = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div x-cloak x-show="passwordModalOpened" class="relative" style="z-index: 1000" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="passwordModalOpened"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 overflow-y-auto" style="z-index: 1000">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="passwordModalOpened"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                    <button @click="passwordModalOpened = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span class="sr-only"><?php echo $langs->trans('VelomaClose'); ?></span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="passrequest" name="passrequest" action="<?php echo dol_buildpath('/veloma/public/index.php', 1); ?>" method="post">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                    <input type="hidden" name="action" value="passrequest">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="">
                            <div class="mt-3 text-center sm:mt-0 sm:my-4 sm:text-left">
                                <h3 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900" id="modal-title"><?php echo $langs->trans('VelomaPasswordRequest'); ?></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500"><?php echo $langs->trans('VelomaPasswordRequestDesc'); ?></p>
                                </div>
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="password-username" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaPhone'); ?></label>
                                        <div class="mt-1">
                                            <input name="password-username" id="password-username" type="text" autocomplete="email" value="<?php echo GETPOST('login-username'); ?>" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaValidate'); ?></button>
                        <button @click="passwordModalOpened = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div x-cloak x-show="accountModalOpened" class="relative" style="z-index: 1000" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="accountModalOpened"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 overflow-y-auto" style="z-index: 1000">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="accountModalOpened"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                    <button @click="accountModalOpened = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span class="sr-only"><?php echo $langs->trans('VelomaClose'); ?></span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="account" name="account" action="<?php echo dol_buildpath('/veloma/public/index.php', 1); ?>" method="post">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                    <input type="hidden" name="action" value="account">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="">
                            <div class="mt-3 text-center sm:mt-0 sm:my-4 sm:text-left">
                                <h3 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900" id="modal-title"><?php echo $langs->trans('VelomaMyAccount'); ?></h3>
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="account-phone" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaPhone'); ?></label>
                                        <div class="mt-1">
                                            <input name="account-phone" id="account-phone" type="tel" autocomplete="tel" value="<?php echo $user->user_mobile; ?>" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="account-firstname" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaFirstName'); ?></label>
                                        <div class="mt-1">
                                            <input name="account-firstname" id="account-firstname" type="text" autocomplete="given-name" value="<?php echo $user->firstname; ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="account-lastname" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaLastName'); ?></label>
                                        <div class="mt-1">
                                            <input name="account-lastname" id="account-lastname" type="text" autocomplete="family-name" value="<?php echo $user->lastname; ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="account-email" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaEmail'); ?></label>
                                        <div class="mt-1">
                                            <input name="account-email" id="account-email" type="email" autocomplete="email" value="<?php echo $user->email; ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="account-password" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaPassword'); ?></label>
                                        <div class="mt-1">
                                            <input name="account-password" id="account-password" type="password" value="" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaValidate'); ?></button>
                        <button @click="accountModalOpened = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div x-cloak x-show="registerModalOpened" class="relative" style="z-index: 1000" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="registerModalOpened"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 overflow-y-auto" style="z-index: 1000">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="registerModalOpened"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                    <button @click="registerModalOpened = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span class="sr-only"><?php echo $langs->trans('VelomaClose'); ?></span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="register" name="register" action="<?php echo dol_buildpath('/veloma/public/index.php', 1); ?>" method="post">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                    <input type="hidden" name="action" value="register">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="">
                            <div class="mt-3 text-center sm:mt-0 sm:my-4 sm:text-left">
                                <h3 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900" id="modal-title"><?php echo $langs->trans('VelomaSignUp'); ?></h3>
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="register-phone" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaPhone'); ?></label>
                                        <div class="mt-1">
                                            <input name="register-phone" id="register-phone" type="tel" autocomplete="tel" value="<?php echo GETPOST('register-phone'); ?>" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="register-firstname" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaFirstName'); ?></label>
                                        <div class="mt-1">
                                            <input name="register-firstname" id="register-firstname" type="text" autocomplete="given-name" value="<?php echo GETPOST('register-firstname'); ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="register-lastname" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaLastName'); ?></label>
                                        <div class="mt-1">
                                            <input name="register-lastname" id="register-lastname" type="text" autocomplete="family-name" value="<?php echo GETPOST('register-lastname'); ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="register-email" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaEmail'); ?></label>
                                        <div class="mt-1">
                                            <input name="register-email" id="register-email" type="email" autocomplete="email" value="<?php echo GETPOST('register-email'); ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaValidate'); ?></button>
                        <button @click="registerModalOpened = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div x-cloak x-show="confirmationModalOpened" class="relative" style="z-index: 1000" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="confirmationModalOpened"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 overflow-y-auto" style="z-index: 1000">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="confirmationModalOpened"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                    <button @click="confirmationModalOpened = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <span class="sr-only"><?php echo $langs->trans('VelomaClose'); ?></span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="register" name="register" action="<?php echo dol_buildpath('/veloma/public/index.php', 1); ?>" method="post">
                    <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                    <input type="hidden" name="action" value="passvalidation">
                    <input type="hidden" name="validation-username" value="<?php echo GETPOST('password-username'); ?>">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="">
                            <div class="mt-3 text-center sm:mt-0 sm:my-4 sm:text-left">
                                <h3 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900" id="modal-title"><?php echo $langs->trans('VelomaPasswordValidation'); ?></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500"><?php echo $langs->trans('VelomaPasswordValidationDesc'); ?></p>
                                </div>
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="validation-code" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaValidationCode'); ?></label>
                                        <div class="mt-1">
                                            <input name="validation-code" id="validation-code" type="text" value="<?php echo GETPOST('validation-code'); ?>" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaValidate'); ?></button>
                        <button @click="confirmationModalOpened = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</div>


</body>

</html>