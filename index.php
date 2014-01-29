<?php

require_once ("java/Java.inc");

$jasperReportsLib = "C:\Program Files (x86)\Java\jre7\lib\\ext";

$handle = @opendir($jasperReportsLib);

$java_library_path = "";
// Add all the Jar file path class (Class Path)
while (($new_item = readdir($handle)) !== false) {
    $java_library_path .= 'file:' . $jasperReportsLib . '/' . $new_item . ';';
}

$jasperConnectString = "jdbc:mysql://localhost:3306/customermgr";
// Loading the libraries classpath


java_require($java_library_path);

// Create the JDBC Connection
$Conn = new Java("org.altic.jasperReports.JdbcConnection");

// Call the driver to be used
$Conn->setDriver("com.mysql.jdbc.Driver");

// Connection URL (since it is in the server, it can be defined as localhost)
$Conn->setConnectString($jasperConnectString);
// Mysql Server Connection Username
$Conn->setUser("root");
// Mysql Server Connection Password
$Conn->setPassword("");

$compileManager = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
$report = $compileManager->compileReport(realpath("report.jrxml"));

$fillManager = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
$sql = "SELECT * FROM CUSTOMER";
$params = new Java("java.util.HashMap");
$params->put("pqQuery", $sql);
//$emptyDataSource = new Java("net.sf.jasperreports.engine.JREmptyDataSource");

$jasperPrint = $fillManager->fillReport($report, $params, $Conn->getConnection());

$outputPath = realpath(".") . "/" . "output.pdf";

$exportManager = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
$exportManager->exportReportToPdfFile($jasperPrint, $outputPath);

header("Content-Type: application/force-download\n");
header("Content-Disposition: attachment; filename=report.pdf");
readfile($outputPath);
//unlink($outputPath);
?>
