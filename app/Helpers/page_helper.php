<?php

function latest($path)
{
    return $path . '?' . @filemtime(ROOTPATH . 'public_html/' . $path);
}

