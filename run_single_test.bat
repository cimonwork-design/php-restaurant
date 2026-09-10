@echo off
chcp 65001 > nul
set PYTHONUNBUFFERED=1
title Kiem Thu Tu Dong - Chon Test Case & Quay Video

echo ==============================================================================
echo       KIEM THU TU DONG FORM TAO PHIEU NHAP KHO (CHON TEST CASE & QUAY VIDEO)
echo ==============================================================================
echo.
echo Cac Test Case mau de quan sat bao loi truc quan:
echo   - Nhap 19 : Test NCC qua ngan (1 ky tu) -> Bao loi do
echo   - Nhap 21 : Test NCC dai hon 100 ky tu  -> (Test Case FAIL mau)
echo   - Nhap 23 : Test bo trong Ngay nhap kho -> Bao loi do
echo   - Nhap 26 : Test Ngay nhap tuong lai    -> Bao loi do
echo   - Nhap 38 : Test trung lap nguyen lieu  -> To do dong trung va bao loi
echo   - Nhap 43 : Test So luong am (-10)      -> Bao loi do
echo   - Nhap 47 : Test So luong 4 so thap phan-> (Test Case FAIL mau)
echo   - Nhap 67 : Test 4 loi vi pham dong thoi-> Khung loi do 4 dong chi tiet
echo   - Hoac nhap so bat ky tu 1 den 73 (Nhan Enter de chay toan bo 73 TCs)
echo ==============================================================================
echo.

set /p TC_CHOICE=">> Nhap ma Test Case muon chay (VD: 19): "

if "%TC_CHOICE%"=="" (
    echo.
    echo [*] Dang chay TOAN BO 73 Test Cases (Co hien thi trinh duyet va QUAY VIDEO)...
    "C:\Users\ducdu\AppData\Local\Programs\Python311\python.exe" -u "%~dp0playwright_inventory_receipt.py" --headed --slowmo=150 --video
) else (
    echo.
    echo [*] Dang chay rieng Test Case TC%TC_CHOICE% (Co hien thi trinh duyet va QUAY VIDEO)...
    "C:\Users\ducdu\AppData\Local\Programs\Python311\python.exe" -u "%~dp0playwright_inventory_receipt.py" --headed --slowmo=250 --video --tc=%TC_CHOICE%
)

echo.
echo ==============================================================================
echo [*] Hoan tat! 
echo [*] Video & Anh da duoc luu tai: testcase_receipt_evidence\
echo [*] File Trace chi tiet da luu tai: trace_receipt.zip
echo [*] Dang mo thu muc chua video va bao cao HTML Dashboard...
echo ==============================================================================

if exist "%~dp0testcase_receipt_evidence" (
    explorer "%~dp0testcase_receipt_evidence"
)
start "" "%~dp0Bao_cao_Kiem_thu_Tao_Phieu_Nhap.html"

echo.
echo Nhan phim bat ky de thoat...
pause > nul
