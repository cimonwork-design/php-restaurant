<?php

function formMessage($key)
{
    $messages = [
        'required' => 'Vui lòng nhập đầy đủ thông tin bắt buộc.',
        'username_required' => 'Tên đăng nhập không được để trống.',
        'username_format' => 'Tên đăng nhập chỉ gồm chữ cái, chữ số, dấu chấm, gạch dưới và gạch ngang.',
        'username_length' => 'Tên đăng nhập phải từ 3 đến 60 ký tự.',
        'password_required' => 'Mật khẩu không được để trống.',
        'password_length' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
        'fullname_required' => 'Họ và tên không được để trống.',
        'fullname_length' => 'Họ và tên không được vượt quá 100 ký tự.',
        'confirm_password_required' => 'Vui lòng nhập mật khẩu xác nhận.',
        'password_mismatch' => 'Mật khẩu xác nhận không khớp.',
        'username_exists' => 'Tên đăng nhập đã tồn tại.',
        'username_not_found' => 'Tên đăng nhập không tồn tại.',
        'password_invalid' => 'Mật khẩu không đúng.',
        'role_invalid' => 'Vai trò không hợp lệ.',
        'code_format' => 'Mã chỉ gồm chữ cái, chữ số, dấu gạch ngang và gạch dưới.',
        'code_length' => 'Mã không được vượt quá 50 ký tự.',
        'code_exists' => 'Mã món đã tồn tại.',
        'name_required' => 'Tên không được để trống.',
        'name_length' => 'Tên không được vượt quá 100 ký tự.',
        'price_invalid' => 'Giá món phải là số lớn hơn 0.',
        'price_required' => 'Giá món không được để trống.',
        'table_invalid' => 'Bàn được chọn không tồn tại hoặc không hợp lệ.',
        'table_required' => 'Vui lòng chọn bàn.',
        'table_not_free' => 'Bàn đang được sử dụng hoặc đã được đặt trước.',
        'customer_required' => 'Tên khách hàng không được để trống.',
        'customer_length' => 'Tên khách hàng không được vượt quá 100 ký tự.',
        'party_invalid' => 'Số khách phải là số nguyên lớn hơn 0.',
        'datetime_invalid' => 'Thời gian không hợp lệ.',
        'end_before_start' => 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu.',
        'reservation_overlap' => 'Bàn đã có đặt chỗ trùng thời gian.',
        'status_invalid' => 'Trạng thái không hợp lệ.',
        'order_empty' => 'Vui lòng chọn ít nhất một món.',
        'menu_not_found' => 'Món ăn không tồn tại.',
        'quantity_invalid' => 'Số lượng món phải là số nguyên lớn hơn 0.',
        'discount_invalid' => 'Giảm giá không được âm hoặc vượt quá tạm tính.',
        'vat_invalid' => 'VAT phải nằm trong khoảng từ 0 đến 100%.',
        'order_time_invalid' => 'Thời gian đơn hàng không hợp lệ.',
        'category_length' => 'Tên danh mục không được vượt quá 100 ký tự.',
        'date_invalid' => 'Ngày không hợp lệ.',
        'date_range_invalid' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.'
    ];
    return $messages[$key] ?? $messages['required'];
}

function validDateValue($value)
{
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return false;
    }

    [$year, $month, $day] = array_map('intval', explode('-', $value));
    return checkdate($month, $day, $year);
}
