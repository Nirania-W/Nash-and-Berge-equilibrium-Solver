# 1. ใช้ Base Image เป็น PHP พร้อม Apache
FROM php:8.2-apache

# 2. อัปเดตและติดตั้ง Python 3 พร้อมกับ Numpy และ Scipy ผ่าน apt-get (เสถียรกว่า pip)
RUN apt-get update && apt-get install -y \
    python3 \
    python3-numpy \
    python3-scipy \
    && rm -rf /var/lib/apt/lists/*

# 3. คัดลอกไฟล์โปรเจกต์ทั้งหมดไปที่โฟลเดอร์เว็บ
COPY . /var/www/html/

# 4. ตั้งค่า Permission ให้ Apache เขียนไฟล์ชั่วคราวได้ (แก้ปัญหา tempnam ใน PHP)
RUN chown -R www-data:www-data /var/www/html

# 5. เปิด Port 80
EXPOSE 80
