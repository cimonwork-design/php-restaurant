@echo off
chcp 65001 > nul
set PYTHONUNBUFFERED=1
title Kiem Thu Tu Dong - Tao Phieu Nhap Kho (Headed Mode)

echo ====================================================================
echo   CHAY KIEM THU TU DONG - HIEN THI TRINH DUYET TRUC QUAN (HEADED)
echo ====================================================================
echo.
echo [*] Dang khoi dong Chromium... Trinh duyet se tu dong mo len man hinh.
echo [*] Tien trinh kiem thu se in truc tiep ben duoi:
echo.

"C:\Users\ducdu\AppData\Local\Programs\Python311\python.exe" -u "%~dp0playwright_inventory_receipt.py" --headed --slowmo=80

echo.
echo ====================================================================
echo [*] Da hoan tat toan bo 73 Test Cases!
echo [*] Dang mo ban bao cao HTML Dashboard tren trinh duyet...
echo ====================================================================
start "" "%~dp0Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html"
echo.
echo Nhan phim bat ky de dong cua so nay...
pause > nul
