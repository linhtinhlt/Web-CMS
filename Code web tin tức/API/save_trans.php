<?php
include '../DATABASE/connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['baivietID']) && !empty($_POST['baivietID']) && isset($_POST['language']) && !empty($_POST['language'])) {
        $baivietID = $_POST['baivietID'];
        $language = $_POST['language'];  

      
        $sql_check = "SELECT * FROM BaivietDich WHERE BaivietID = ? AND NgonNgu = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("is", $baivietID, $language);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

    
        if ($result_check->num_rows > 0) {

            $row = $result_check->fetch_assoc();
            $tieude_dich = $row['TieudeDich'];
            $tomtat_dich = $row['TomtatDich'];
            $noidung_dich = $row['NoidungDich'];

           
            $sql = "SELECT * FROM Baiviet WHERE BaivietID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $baivietID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $tieude = $row['Tieude'];
                $tomtat = $row['Tomtat'];
                $noidung = $row['Noidung'];

               
                if ($tieude != $row['Tieude'] || $tomtat != $row['Tomtat'] || $noidung != $row['Noidung']) {
                    $texts = [$tieude, $tomtat, $noidung];
                    $fromLanguage = 'vi';  
                    $toLanguage = $language; 

                   
                    $url = 'http://localhost/test/API/translate.php'; 
                    $postData = [
                        'text' => json_encode($texts),
                        'from' => $fromLanguage,
                        'to' => $toLanguage
                    ];

                   
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                    
                    $response = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($http_code != 200) {
                        echo json_encode([ 
                            'status' => 'error',
                            'message' => 'Lỗi khi kết nối với dịch vụ dịch. Mã lỗi HTTP: ' . $http_code,
                        ]);
                        exit;
                    }

                    $data = json_decode($response, true);
                    if (isset($data) && is_array($data) && isset($data[0]['translations'][0]['text'])) {
                      
                        $tieude_dich = $data[0]['translations'][0]['text'];
                        $tomtat_dich = $data[1]['translations'][0]['text'];
                        $noidung_dich = $data[2]['translations'][0]['text'];

                        
                        $sql_update = "UPDATE BaivietDich SET TieudeDich = ?, TomtatDich = ?, NoidungDich = ? 
                                       WHERE BaivietID = ? AND NgonNgu = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bind_param("sssis", $tieude_dich, $tomtat_dich, $noidung_dich, $baivietID, $language);

                        
                        if ($stmt_update->execute()) {
                            echo json_encode([
                                'status' => 'success',
                                'message' => 'Bài viết đã được dịch lại và bản dịch đã được cập nhật.',
                            ]);
                        } else {
                            echo json_encode([
                                'status' => 'error',
                                'message' => 'Lỗi khi cập nhật bản dịch vào cơ sở dữ liệu. Mã lỗi: ' . $stmt_update->error,
                            ]);
                        }
                    } else {
                       
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Dịch không thành công. Phản hồi từ dịch vụ dịch: ' . $response,
                        ]);
                    }
                } else {
                    
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Bài viết chưa có thay đổi nội dung, không cần dịch lại.',
                    ]);
                }
            }
        } else {
           
            $sql = "SELECT * FROM Baiviet WHERE BaivietID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $baivietID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $tieude = $row['Tieude'];
                $tomtat = $row['Tomtat'];
                $noidung = $row['Noidung'];

                
                $texts = [$tieude, $tomtat, $noidung];
                $fromLanguage = 'vi';  
                $toLanguage = $language;  

               
                $url = 'http://localhost/test/API/translate.php';  
                $postData = [
                    'text' => json_encode($texts),
                    'from' => $fromLanguage,
                    'to' => $toLanguage
                ];

             
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

               
                if ($http_code != 200) {
                    echo json_encode([ 
                        'status' => 'error',
                        'message' => 'Lỗi khi kết nối với dịch vụ dịch. Mã lỗi HTTP: ' . $http_code,
                    ]);
                    exit;
                }

                $data = json_decode($response, true);

               
                if (isset($data) && is_array($data) && isset($data[0]['translations'][0]['text'])) {
                    
                    $tieude_dich = $data[0]['translations'][0]['text'];
                    $tomtat_dich = $data[1]['translations'][0]['text'];
                    $noidung_dich = $data[2]['translations'][0]['text'];

                   
                    $sql_insert = "INSERT INTO BaivietDich (BaivietID, NgonNgu, TieudeDich, TomtatDich, NoidungDich) 
                                   VALUES (?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("issss", $baivietID, $language, $tieude_dich, $tomtat_dich, $noidung_dich);

            
                    if ($stmt_insert->execute()) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Bản dịch đã được lưu vào cơ sở dữ liệu.',
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Lỗi khi lưu bản dịch vào cơ sở dữ liệu. Mã lỗi: ' . $stmt_insert->error,
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Dịch không thành công. Phản hồi từ dịch vụ dịch: ' . $response,
                    ]);
                }
            }
        }
    } else {
        $missingData = [];
        if (empty($_POST['baivietID'])) {
            $missingData[] = 'baivietID';
        }
        if (empty($_POST['language'])) {
            $missingData[] = 'language';
        }

        echo json_encode([
            'status' => 'error',
            'message' => 'Thiếu ' . implode(', ', $missingData) . ' trong yêu cầu.',
        ]);
    }
}
?>
