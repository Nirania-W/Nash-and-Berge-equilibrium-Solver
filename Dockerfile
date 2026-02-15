# 1. ใช้ Base Image เป็น PHP พร้อม Apache (เว็บเซิร์ฟเวอร์)
FROM php:8.2-apache

# 2. ลงโปรแกรม Python 3 และ Pip ในเครื่องจำลอง
RUN apt-get update && apt-get install -y python3 python3-pip

# 3. แก้ปัญหา Permission ของ Python ใน Docker (เพื่อให้ลง library ได้)
RUN rm /usr/lib/python3.11/EXTERNALLY-MANAGED || true

# 4. ก๊อปปี้ไฟล์ requirements.txt ไปเตรียมลง Library
COPY requirements.txt /var/www/html/

# 5. สั่งลง NumPy และ SciPy
RUN pip3 install -r /var/www/html/requirements.txt

# 6. ก๊อปปี้ไฟล์โปรเจกต์ทั้งหมด (index.php, solver.py, css) ไปที่โฟลเดอร์เว็บ
COPY . /var/www/html/

# 7. เปิด Port 80 ให้คนอื่นเข้าใช้งาน
EXPOSE 80
