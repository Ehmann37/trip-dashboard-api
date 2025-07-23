<?php

function buildWhereClause(array $filters, array &$params): string {
  $clauses = [];

  foreach ($filters as $column => $value) {
      if (!is_null($value)) {
          $paramKey = str_replace(['.', '(', ')'], '_', $column);
          $clauses[] = "$column = :$paramKey";
          $params[$paramKey] = $value;
      }
  }

  return $clauses ? ' AND ' . implode(' AND ', $clauses) : '';
}


function updateRecord($table, $idField, $id, $data, $allowedFields) {
  global $pdo;

  $fieldsToUpdate = [];
  $params = [":$idField" => $id];

  foreach ($allowedFields as $field) {
    if (array_key_exists($field, $data)) {
      $fieldsToUpdate[] = "$field = :$field";
      $params[":$field"] = $data[$field];  // Corrected here
    }
  }

  if (empty($fieldsToUpdate)) {
    return false;
  }

  $sql = "UPDATE $table SET " . implode(', ', $fieldsToUpdate) . " WHERE $idField = :$idField";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);

  return $stmt->rowCount() > 0;
}


function checkExists($table, $field, $value, $extraConditions = []): bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM $table WHERE $field = ?";
  $params = [$value];

  foreach ($extraConditions as $key => $val) {
    $sql .= " AND $key = ?";
    $params[] = $val;
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);

  return $stmt->fetchColumn() > 0;
}

function insertRecord(string $table, array $data): ?int {
  global $pdo;

  if (empty($data)) return null;

  $columns = array_keys($data);

  $placeholders = array_map(fn($col) => ":$col", $columns);

  $sql = "INSERT INTO $table (" . implode(', ', $columns) . ")
          VALUES (" . implode(', ', $placeholders) . ")";

  $stmt = $pdo->prepare($sql);

  foreach ($data as $col => $value) {
      $stmt->bindValue(":$col", $value ?? null);
  }

  if ($stmt->execute()) {
      return (int)$pdo->lastInsertId();
  }

  return null;
}

function updateRecordByCondition($table, $whereConditions, $updateData) {
  global $pdo;

  $setClause = [];
  $whereClause = [];
  $params = [];

  foreach ($updateData as $field => $value) {
    $setClause[] = "$field = :set_$field";
    $params[":set_$field"] = $value;
  }

  foreach ($whereConditions as $field => $value) {
    $whereClause[] = "$field = :where_$field";
    $params[":where_$field"] = $value;
  }

  $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);
  $stmt = $pdo->prepare($sql);

  return $stmt->execute($params);
}
