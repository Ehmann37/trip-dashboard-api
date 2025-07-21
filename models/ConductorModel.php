  <?php 
  require_once __DIR__ . '/../config/db.php';
  require_once __DIR__ . '/../utils/DBUtils.php';

  function getAllConductors(){
    global $pdo;
    
    $sql = "SELECT * FROM users WHERE role = 'conductor'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $conductors = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($conductors as &$conductor) {
      $conductor['conductor_id'] = intval($conductor['user_id']);

      unset($conductor['hashed_password'], $conductor['company_id'], $conductor['created_at'], $conductor['token'], $conductor['role'], $conductor['user_id']);

      $sql = "SELECT bus_id FROM bus WHERE conductor_id = :conductor_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([':conductor_id' => $conductor['conductor_id']]);

      $bus_id = $stmt->fetchColumn();
      
      if ($bus_id) {
        $conductor['status'] = 'Active';
        $conductor['bus_id'] = $bus_id;
      } else {
        $conductor['status'] = 'Inactive';
        $conductor['bus_id'] = null;
      }
    }
    echo json_encode($conductors);
    exit;
  }

  function addConductor(array $conductorData): int {
    $conductorData['hashed_password'] = password_hash('123123123', PASSWORD_DEFAULT); 
    $conductorData['role'] = 'conductor';
    $conductorData['created_at'] = date('Y-m-d H:i:s');

    return insertRecord('users', $conductorData);
  }