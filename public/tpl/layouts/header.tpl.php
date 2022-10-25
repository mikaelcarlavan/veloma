<?php

// Displays title
$appli = $langs->trans('VelomaApplicationTitle');
$titletoshow = dol_htmlentities($appli);

$url = dol_buildpath('/veloma/public/index.php', 2);

$openConfirmationModal = isset($openConfirmationModal) ? $openConfirmationModal : false;

$favicon = DOL_URL_ROOT.'/theme/dolibarr_256x256_color.png';
if (!empty($mysoc->logo_squarred_mini)) {
    $favicon = DOL_URL_ROOT.'/viewimage.php?cache=1&modulepart=mycompany&file='.urlencode('logos/thumbs/'.$mysoc->logo_squarred_mini);
}
if (!empty($conf->global->MAIN_FAVICON_URL)) {
    $favicon = $conf->global->MAIN_FAVICON_URL;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Primary Meta Tags -->
    <title><?php echo $titletoshow; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="<?php echo $appli; ?>">
    <meta name="author" content="<?php echo $appli; ?>">
    <meta name="description" content="<?php echo $langs->trans('VelomaMetaDescription'); ?>">
    <meta name="keywords" content="<?php echo $langs->trans('VelomaMetaKeywords'); ?>" />
    
    <link rel="canonical" href="<?php echo $url; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $url; ?>">
    <meta property="og:title" content="<?php echo $appli; ?>">
    <meta property="og:description" content="<?php echo $langs->trans('VelomaMetaDescription'); ?>">
    <meta property="og:image" content="<?php echo $favicon; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $url; ?>">
    <meta property="twitter:title" content="<?php echo $appli; ?>">
    <meta property="twitter:description" content="<?php echo $langs->trans('VelomaMetaDescription'); ?>">
    <meta property="twitter:image" content="<?php echo $favicon; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $favicon; ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
          integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14="
          crossorigin=""/>

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css"
          crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css"
          crossorigin=""/>
    <!-- JS -->
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js"
            integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg="
            crossorigin=""></script>

    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"
            crossorigin=""></script>
    <style>
        [x-cloak=""] {
            display: none;
        }
    </style>
</head>

<body>

<div class="relative bg-gray-50" x-data="{loginModalOpened: false, passwordModalOpened: false, registerModalOpened: false, accountModalOpened: false, confirmationModalOpened: <?php echo $confirmationModalOpened ? 'true' : 'false'; ?>}">

    <?php include_once('nav.tpl.php'); ?>

    <main class="lg:relative">
        <div class="mx-auto w-full max-w-7xl pt-16 pb-20 text-center lg:py-48 lg:text-left">

            <?php include_once('error.tpl.php'); ?>
 