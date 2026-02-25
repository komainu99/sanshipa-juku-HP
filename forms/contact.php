<?php
// セキュリティヘッダーを追加
header('Content-Type: application/json; charset=UTF-8');

// デバッグ用：エラーログを有効にする
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 入力値のサニタイゼーション（セキュリティ強化）
    $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
    $plan = htmlspecialchars(trim($_POST['plan'] ?? ''), ENT_QUOTES, 'UTF-8');
    $start_time = htmlspecialchars(trim($_POST['start_time'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    // バリデーション
    $errors = [];
    
    if (empty($last_name)) {
        $errors[] = '姓を入力してください。';
    }
    
    if (empty($first_name)) {
        $errors[] = '名を入力してください。';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '有効なメールアドレスを入力してください。';
    }
    
    if (empty($phone)) {
        $errors[] = '電話番号を入力してください。';
    }
    
    if (empty($plan)) {
        $errors[] = 'プランを選択してください。';
    }
    
    if (empty($start_time)) {
        $errors[] = '受講開始希望時期を選択してください。';
    }
    
    // エラーがある場合
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => implode(' ', $errors)
        ]);
        exit;
    }
    
    // 送信先メールアドレス
    $to = 'hidekazu419@gmail.com';
    
    // メールの件名（セキュリティ対策）
    $subject = mb_encode_mimeheader('入塾テスト申し込み - ' . $last_name . ' ' . $first_name, 'UTF-8');
    
    // メール本文
    $mail_body = "新しい入塾テストの申し込みがあります。\n\n";
    $mail_body .= "姓: " . $last_name . "\n";
    $mail_body .= "名: " . $first_name . "\n";
    $mail_body .= "メールアドレス: " . $email . "\n";
    $mail_body .= "電話番号: " . $phone . "\n";
    $mail_body .= "希望プラン: " . $plan . "\n";
    $mail_body .= "受講開始希望時期: " . $start_time . "\n";
    $mail_body .= "メッセージ: " . $message . "\n";
    $mail_body .= "\n送信日時: " . date('Y-m-d H:i:s') . "\n";
    
    // メールヘッダー（セキュリティ強化）
    $headers = "From: noreply@yourdomain.com\r\n"; // 実際のドメインに変更
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // メール送信（エラーハンドリング強化）
    try {
        // デバッグ用：メール内容をログに記録
        error_log("=== メール送信試行 ===");
        error_log("送信先: " . $to);
        error_log("件名: " . $subject);
        error_log("本文: " . $mail_body);
        error_log("ヘッダー: " . $headers);
        
        $mail_result = mail($to, $subject, $mail_body, $headers);
        
        error_log("mail()関数の戻り値: " . ($mail_result ? 'true' : 'false'));
        
        if ($mail_result) {
            // ログ記録（オプション）
            error_log("Contact form submitted: " . $email . " - " . date('Y-m-d H:i:s'));
            
            echo json_encode([
                'success' => true, 
                'message' => 'メッセージが送信されました。ありがとうございます！'
            ]);
        } else {
            error_log("メール送信失敗: mail()がfalseを返しました");
            echo json_encode([
                'success' => false, 
                'message' => 'メール送信に失敗しました。システム管理者にお問い合わせください。'
            ]);
        }
    } catch (Exception $e) {
        error_log("Mail sending error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'システムエラーが発生しました: ' . $e->getMessage()
        ]);
    }
    
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'POSTメソッドのみ許可されています。'
    ]);
}
?>
