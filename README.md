<img src="assets/imgs/logo.png" alt="logo">
<h1>SQHS Employee Center (โครงการหงสา)</h1>
<p>แอพพลิเคชั่นสำหรับพนักงาน เพื่อการเข้าถึงข้อมูลและบริการต่างๆ ของบริษัทได้อย่างรวดเร็วและสะดวกสบาย</p>

<h1>Feature ปัจจุบัน (Update : 16/10/2025)</h1>
<p>
     $${\color{red}  
    *หมายเหตุ : มือถือ /แท็บเล็ต  Android ในบางยี่ห้อหรือบางรุ่น เช่น Huawei , Redmi 
      }$$
      <br>
    $${\color{red}  
      ไม่สามารถกดติดตั้งได้ เนื่องจากไม่รองรับ Google Service แต่ยังสามารถใช้งานผ่านเว็บบราวเซอร์ได้*
      }$$
</p>
<ul>
  <li>สามารถติดตั้งบนคอมฯ หรือ มือถือ / แท็บเล็ต Android ได้ จากปุ่มติดตั้งที่อยู่หน้า Login</li>
  <li>การติดตั้งบน iOS /iPad ให้กดเลือก ปุ่มแชร์ > กดที่ปุ่ม เพิ่มไปยังโฮม </li>
  <li>รองรับการแจ้งเตือนแบบ Webpush ทั้งบนคอม ฯ และมือถือ (ผู้ใช้ต้องกดรับการแจ้งเตือนเอง และสามารถยกเลิกได้ในหน้าการตั้งค่า)</li>
  <li>สามารถจัดการการแจ้งเตือนของแต่ละอุปกรณ์ได้</li>
  <li>สามารถจัดการการเข้าสู่ระบบของแต่ละอุปกรณ์ได้</li>
</ul>

<h1>รายการการให้บริการปัจจุบัน (Update : 16/10/2025)</h1>
<ul>
  <li>
    $${\color{red} *หมายเหตุ : ในการเข้าใช้งานแต่ละบริการผู้ใช้งานจะต้องกรอกรหัสผ่านของตนทุกครั้ง*
    }$$
  </li>
  <li>ข้อมูลทางการแพทย์ เช่นการแพ้อาหาร , ยา หรือ สัตว์</li>
  <li>ข้อมูลประวัติใบเตือน</li>
  <li>ข้อมูลวันลาของพนักงานในแต่ละปี</li>
  <li>ข้อมูลผลการประเมินการทำงานในแต่ละปี</li>
  <li>คู่มือการปฏิบัติงานอย่างปลอดภัย (SHE Handbook)</li>
</ul>

<h3>การติดตั้ง/ใช้งาน</h3>
<ol>
  <li>ทำการดาวน์โหลดหรือ clone โปรเจ็คนี้</li>
  <li>ก็อปปี้ไปวางที่ www หรือ htdocs (หากใช้ Xampp)</li>
  <li>ปิด cmd รันคำส่ง cd sqhs-empcenter เพื่อเข้าสู่โฟลเดอร์ของโปรเจ็ค</li>
  <li>รันคำส่ง composer install เพื่อติดตั้ง dependencies</li>
  <li>รันคำส่ง cd configs เพื่อเข้าสู่โฟลเดอร์ configs</li>
  <li>รันคำส่ง php mkvapid เพื่อสร้าง public และ private key (สำหรับใช้ใน .env)</li>
  <li>สร้างไฟล์ .env ใน root ของโฟลเดอร์โปรเจ็ค</li>
  <p>โดยมีรายละเอียดดังนี้</p>
  <code>
      APP_NAME= sqhs-emp-center
      APP_DOMAIN = domain ที่ใช้ host (local / dev ใช้ localhost)
      BASE_PATH = /sqhs-empcenter 
      DB_DSN = pgsql 
      DB_HOST= ไอพีของฐานข้อมูล
      DB_DATABASE1=ชื่อฐานข้อมูลของพนักงาน
      DB_DATABASE2=ชื่อฐานข้อมูลของวันหยุด
      DB_USERNAME= ชื่อผู้ใช้งานฐานข้อมูล
      DB_PASSWORD=รหัสผ่านฐานข้อมูล 
      DB_PORT= 5432
      FTP_SERVER = ไอพีของ FTP Server
      FTP_USERNAME = ชื่อผู้ใช้งาน FTP Server
      FTP_PASSWORD = รหัสผ่าน FTP Server
      VAPID_PUBLIC_KEY =  Public Key ที่ถูกสร้างในข้อ 6 (สำหรับใช้งานกับระบบ Webpush)
      VAPID_PRIVATE_KEY = Private Key ที่ถูกสร้างในข้อ 6 (สำหรับใช้งานกับระบบ Webpush)
      SECRET_KEY = คีย์ลับสำหรับในกับ JWT สามารถใช้เว็บนี้สร้างได้ https://it-tools.tech/token-generator?length=64
  </code>
</ol>
