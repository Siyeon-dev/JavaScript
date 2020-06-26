<?php
class db_info {
    const DB_URL  = "127.0.0.1";
    const USER_ID = "root";
    const PASSWD  = "Kyanite1!";
    const DB_NAME = "ycj_test";
}

$conn = new mysqli(db_info::DB_URL, db_info::USER_ID
    ,db_info::PASSWD, db_info::DB_NAME);

if($conn->connect_errno) {
    echo json_encode("DB연결 실패");
    exit(-1);
} else {
    $receivedData = json_decode(file_get_contents('php://input'));
    ////////////////////////////////////////////////////////////
    /// $receivedData[0] 에는 학생 정보, [1] 에는 유저 선택 쿼리문이 담겨있다.
    // <<-- $receivedData[0] 에 Data 가 존재하는 경우, 학생을 INSERT 한다.
    if($receivedData[0] != null && $receivedData[0]->name != "") {
        $id             = $receivedData[0]->id;
        $name           = $receivedData[0]->name;
        $gradeJava      = $receivedData[0]->courseGrade[0];
        $gradeDB        = $receivedData[0]->courseGrade[1];
        $gradeJapanese  = $receivedData[0]->courseGrade[2];
        $pointSum       = $receivedData[0]->sum;
        $pointAvg       = $receivedData[0]->avg;

        // 학생 데이터 삽입 쿼리문
        $queryInsertStd = "INSERT INTO gpa VALUES ($id, '$name', $gradeJava, $gradeDB, $gradeJapanese, $pointSum, $pointAvg);";
        if (!$conn->query($queryInsertStd)) {
            $queryResult = "failed to insert user data";
        }
    } // -->> $receivedData[0] 에 Data 가 존재하는 경우, 학생을 INSERT 한다.
    ////////////////////////////////////////////////////////////

    $querySelectStd ="";    // switch 문에 따른 쿼리문이 담기는 변수

    switch ($receivedData[1]) {
        case "insert" :
            $querySelectStd = "SELECT * FROM gpa;";
            break;
        case "ascend" :
            $querySelectStd = "SELECT * FROM gpa ORDER BY pointSum;";
            break;
        case "descend" :
            $querySelectStd = "SELECT * FROM gpa ORDER BY pointSum DESC;";
            break;
    }
    // 쿼리문 실행 후 해당 records 를 변수 $result 에 저장
    $result = $conn->query($querySelectStd);

    $resultArray=[];  // 최종 쿼리문이 담길 배열
    // 쿼리 결과 각각의 row(2차원 배열) 을 Web Browser 에게 response 할 배열에 담는다.
    for($i = 0; $rows = $result->fetch_all(); $i++) {
        $resultArray[$i] = $rows;
    }

    // 최종 결과 반환
    echo json_encode($resultArray);
}

?>