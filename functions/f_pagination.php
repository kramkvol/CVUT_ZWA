<?php
/**
 * Retrieve paginated data using a fetch function and a total count function.
 *
 * @param mysqli $conn The database connection object.
 * @param callable $fetchFunction A callback function to fetch data for the current page. 
 *                                It should accept $conn, $offset, and $recordsPerPage as arguments.
 * @param callable $totalFunction A callback function to get the total number of records.
 *                                It should accept $conn as an argument.
 * @param int $recordsPerPage The number of records to display per page.
 * @return array An associative array containing:
 *               - 'items': The fetched items for the current page.
 *               - 'totalPages': The total number of pages.
 *               - 'currentPage': The current page number.
 */
function getPaginationData($conn, $fetchFunction, $totalFunction, $recordsPerPage) {
    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    $totalItems = call_user_func($totalFunction, $conn); 
    if ($totalItems === 0) {
        return [
            'items' => [],
            'totalPages' => 0,
            'currentPage' => $currentPage
        ];
    }
    
    $items = call_user_func($fetchFunction, $conn, $offset, $recordsPerPage);
    $totalPages = ceil($totalItems / $recordsPerPage);
    
    return [
        'items' => $items,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage
    ];
}
 
?>