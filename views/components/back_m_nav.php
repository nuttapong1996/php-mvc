<?php
function goBackMnav($page)
{
  echo "<nav id='mobile-nav' class='justify-content-center align-items-center p-1 '>
    <a href='$page' class='d-flex flex-column text-white text-decoration-none '>
      <i class='fas fa-chevron-circle-left mb-2'></i>
      <span class='small'>กลับ</span>
    </a>  
  </nav>";
}
