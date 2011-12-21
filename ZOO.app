<?php

$app_desc = array(
    "name"          => "ZOO", //Name
    "short_name"    => N_("Zoo"), //Short name
    "description"   => N_("Zoo formation"), //long description
    "access_free"   => "N", //Access free ? (Y,N)
    "icon"          => "zoo.png", //Icon
    "displayable"   => "Y", //Should be displayed on an app list (Y,N)
    "with_frame"    => "Y", //Use multiframe ? (Y,N)
    "childof"       => "ONEFAM" // instance of FREEDOM GENERIC application
);

/* available ACLs */
$app_acl = array(
    array(
        "name"               => "ZOO_MONEY",
        "description"        => N_("Access to ticket sales")
    )
);

/* Available actions */
$action_desc = array(
    array(
        "name"              => "ZOO_ANIMALFOLDER",
        "short_name"        => N_("animal folder"),
        "acl"               => "ONEFAM"
    ),
    array(
        "name"              => "ZOO_COLOR",
        "short_name"        => N_("table colors"),
        "acl"               => "ONEFAM_READ"
    ),
    array(
        "name"              => "ZOO_ROOT",
        "short_name"        => N_("entrance"),
        "acl"               => "ONEFAM_READ",
        "root"              => "Y"
    ),
    array(
        "name"             => "ONEFAM_ROOT",
        "root"             => "N"
    )
)

?>