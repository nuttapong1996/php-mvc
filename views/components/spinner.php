<?php
function spinner($textcolor)
{
    $stmt = "<div id='loading' style='display:none; text-align:center; margin:20px;'>
                <div class='spinner-border $textcolor' role='status'>
                    <span class='visually-hidden'>Loading...</span>
                </div>
            </div>";
    echo  $stmt;
}
