@echo off
chcp 65001 > nul
title Playwright Trace Viewer - Quan Sat Kiem Thu Chi Tiet

echo ==============================================================================
echo        MO UNG DUNG PLAYWRIGHT TRACE VIEWER (THEO DOI KIEM THU CHI TIET)
echo ==============================================================================
echo.
echo [*] Dang khoi dong Playwright Trace Viewer tren trinh duyet...
echo [*] Ban co the tua timeline tung giay, xem DOM snapshot, network request va console log.
echo.

if exist "%~dp0trace_receipt.zip" (
    "C:\Users\ducdu\AppData\Local\Programs\Python311\python.exe" -m playwright show-trace "%~dp0trace_receipt.zip"
) else (
    echo [!] Chua tim thay file trace_receipt.zip. Vui long chay kiem thu truoc!
    pause
)
