// This file MUST be in every page EXCEPT Log in page 
import { refreshAccessToken } from './module/tokenControl.js';
import { loadEmpData } from './module/userProfile.js';
import { empPic } from './module/userPic.js';
import { subDisplay } from './module/subscriptionControl.js';
import { highlightCurrentPage } from './module/nav.js';

highlightCurrentPage();
document.addEventListener('DOMContentLoaded', async () => {

  //Register Service Worker to browser//
  navigator.serviceWorker.register("./service-worker.js");

  // check Subscription status
  await subDisplay('btnSub', 'txSub');

  // Load Emp Data 
  await loadEmpData('txEmpname', 'txEmpcode', 'txPosition', 'txDept', 'txNat', 'txPjt');
 
  // Load Emp pic
  empPic('empImg');

  // Check Access Token and renew Refresh Token //
  setInterval(async () => { // If user still active (while using an Appplication) then Refresh Access token every 5 minnute .
    refreshAccessToken();
  }, 5 * 60 * 1000);
});


