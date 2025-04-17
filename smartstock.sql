-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: smartstock
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS customer;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE customer (
  CustomerID varchar(10) NOT NULL,
  CompanyName varchar(100) DEFAULT NULL,
  CustomerType varchar(50) DEFAULT NULL,
  SalesRepID varchar(10) DEFAULT NULL,
  BillingAddress varchar(200) DEFAULT NULL,
  ShippingAddress varchar(200) DEFAULT NULL,
  Email varchar(100) DEFAULT NULL,
  PhoneNumber varchar(20) DEFAULT NULL,
  PRIMARY KEY (CustomerID),
  KEY SalesRepID (SalesRepID),
  CONSTRAINT customer_ibfk_1 FOREIGN KEY (SalesRepID) REFERENCES salesrepresentative (SalesRepID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES customer WRITE;
/*!40000 ALTER TABLE customer DISABLE KEYS */;
INSERT INTO customer VALUES ('CUST001','Apex Instruments','Wholesale','SR1001','1450 Elm St, Dallas, TX','1450 Elm St, Dallas, TX','orders@apexinst.com','214-555-0191\r'),('CUST002','Vertex Robotics','Distributor','SR1002','908 Maple Ave, Austin, TX','910 Maple Ave, Austin, TX','sales@vertexrobotics.io','512-555-0876\r'),('CUST003','Luna Tech Solutions','Retail','SR1003','321 Birch Rd, Plano, TX','321 Birch Rd, Plano, TX','luna.solutions@gmail.com','469-555-2233\r'),('CUST004','Orion Labs Inc.','Distributor','SR1004','77 Horizon Pkwy, Irving, TX','80 Horizon Pkwy, Irving, TX','contact@orionlabsinc.com','972-555-7711\r'),('CUST005','BluePeak Systems','Wholesale','SR1002','1122 Northwood Dr, Frisco','1124 Northwood Dr, Frisco','info@bluepeaksys.com','469-555-4321\r'),('CUST006','Nova Build Co.','Retail','SR1005','78 Silver Oak Ln, Garland','78 Silver Oak Ln, Garland','novabuild@email.com','214-555-9012\r'),('CUST007','Tranquil Softwares','Retail','SR1003','650 Summit Dr, Richardson','651 Summit Dr, Richardson','support@tranquilsoft.com','972-555-3333\r'),('CUST008','Evolve Dynamics','Wholesale','SR1001','430 Hillcrest Rd, McKinney','431 Hillcrest Rd, McKinney','hello@evolvedyn.com','214-555-8877\r'),('CUST009','Indigo Circuits','Distributor','SR1004','99 Cedar Loop, Denton, TX','100 Cedar Loop, Denton, TX','indigo.circuits@yahoo.com','940-555-1717\r'),('CUST010','Stellar Mechanics','Wholesale','SR1005','1200 Polaris Ln, Allen, TX','1202 Polaris Ln, Allen, TX','contact@stellarmech.com','469-555-7777\r');
/*!40000 ALTER TABLE customer ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS items;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE items (
  ItemID varchar(10) NOT NULL,
  ItemName varchar(100) DEFAULT NULL,
  ItemType varchar(50) DEFAULT NULL,
  LocationID varchar(10) DEFAULT NULL,
  PurchasePrice decimal(10,2) DEFAULT NULL,
  OnHandQuantity int(11) DEFAULT NULL,
  SupplierID varchar(10) DEFAULT NULL,
  SKU varchar(50) DEFAULT NULL,
  UPC varchar(50) DEFAULT NULL,
  PRIMARY KEY (ItemID),
  KEY SupplierID (SupplierID),
  KEY LocationID (LocationID),
  CONSTRAINT items_ibfk_1 FOREIGN KEY (SupplierID) REFERENCES supplier (SupplierID) ON DELETE SET NULL,
  CONSTRAINT items_ibfk_2 FOREIGN KEY (LocationID) REFERENCES location (LocationID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES items WRITE;
/*!40000 ALTER TABLE items DISABLE KEYS */;
/*!40000 ALTER TABLE items ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS location;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE location (
  LocationID varchar(10) NOT NULL,
  LocationName varchar(100) DEFAULT NULL,
  LocationType varchar(50) DEFAULT NULL,
  Address varchar(200) DEFAULT NULL,
  PRIMARY KEY (LocationID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES location WRITE;
/*!40000 ALTER TABLE location DISABLE KEYS */;
/*!40000 ALTER TABLE location ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderitems`
--

DROP TABLE IF EXISTS orderitems;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE orderitems (
  OrderID int(11) NOT NULL,
  ItemID varchar(10) NOT NULL,
  POID varchar(10) DEFAULT NULL,
  UnitPrice decimal(10,2) DEFAULT NULL,
  QuantityOrdered int(11) DEFAULT NULL,
  PRIMARY KEY (OrderID,ItemID),
  KEY ItemID (ItemID),
  KEY POID (POID),
  CONSTRAINT orderitems_ibfk_1 FOREIGN KEY (ItemID) REFERENCES items (ItemID),
  CONSTRAINT orderitems_ibfk_2 FOREIGN KEY (POID) REFERENCES purchaseorders (POID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderitems`
--

LOCK TABLES orderitems WRITE;
/*!40000 ALTER TABLE orderitems DISABLE KEYS */;
/*!40000 ALTER TABLE orderitems ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchaseorders`
--

DROP TABLE IF EXISTS purchaseorders;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE purchaseorders (
  POID varchar(10) NOT NULL,
  CustomerID varchar(10) DEFAULT NULL,
  OrderID varchar(10) DEFAULT NULL,
  DeliveryDate date DEFAULT NULL,
  Quantity int(11) DEFAULT NULL,
  PRIMARY KEY (POID),
  KEY CustomerID (CustomerID),
  CONSTRAINT purchaseorders_ibfk_1 FOREIGN KEY (CustomerID) REFERENCES customer (CustomerID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchaseorders`
--

LOCK TABLES purchaseorders WRITE;
/*!40000 ALTER TABLE purchaseorders DISABLE KEYS */;
INSERT INTO purchaseorders VALUES ('PO1019','CUST008','ORD2001','2025-04-21',30),('PO1026','CUST007','ORD2011','2025-04-20',22),('PO1035','CUST005','ORD2004','2025-05-01',14),('PO1044','CUST001','ORD2010','2025-04-24',11),('PO1052','CUST009','ORD2008','2025-04-25',17),('PO1068','CUST004','ORD2012','2025-05-03',25),('PO1071','CUST006','ORD2009','2025-04-27',39),('PO1087','CUST003','ORD2014','2025-05-02',42),('PO1093','CUST002','ORD2017','2025-04-29',8),('PO1105','CUST010','ORD2003','2025-04-23',19),('PO9999','CUST008','ORD9999','2025-04-23',150);
/*!40000 ALTER TABLE purchaseorders ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salesrepresentative`
--

DROP TABLE IF EXISTS salesrepresentative;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE salesrepresentative (
  SalesRepID varchar(10) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  Email varchar(100) DEFAULT NULL,
  PhoneNumber varchar(20) DEFAULT NULL,
  PRIMARY KEY (SalesRepID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salesrepresentative`
--

LOCK TABLES salesrepresentative WRITE;
/*!40000 ALTER TABLE salesrepresentative DISABLE KEYS */;
INSERT INTO salesrepresentative VALUES ('SR1001','Rachel Nguyen','r.nguyen@salesforce.com','214-555-1010\r'),('SR1002','James Patel','j.patel@salesforce.com','512-555-2020\r'),('SR1003','Emily Johnson','e.johnson@salesforce.com','469-555-3030\r'),('SR1004','Daniel Kim','d.kim@salesforce.com','972-555-4040\r'),('SR1005','Sarah Lopez','s.lopez@salesforce.com','214-555-5050\r');
/*!40000 ALTER TABLE salesrepresentative ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS supplier;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE supplier (
  SupplierID varchar(10) NOT NULL,
  SupplierName varchar(100) DEFAULT NULL,
  SalesRepID varchar(10) DEFAULT NULL,
  Address varchar(200) DEFAULT NULL,
  Email varchar(100) DEFAULT NULL,
  PhoneNumber varchar(20) DEFAULT NULL,
  PRIMARY KEY (SupplierID),
  KEY SalesRepID (SalesRepID),
  CONSTRAINT supplier_ibfk_1 FOREIGN KEY (SalesRepID) REFERENCES salesrepresentative (SalesRepID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES supplier WRITE;
/*!40000 ALTER TABLE supplier DISABLE KEYS */;
/*!40000 ALTER TABLE supplier ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferorders`
--

DROP TABLE IF EXISTS transferorders;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE transferorders (
  TransferID varchar(10) NOT NULL,
  TransferDate date DEFAULT NULL,
  FromLocation varchar(10) DEFAULT NULL,
  ToLocation varchar(10) DEFAULT NULL,
  ItemID varchar(10) DEFAULT NULL,
  Quantity int(11) DEFAULT NULL,
  PRIMARY KEY (TransferID),
  KEY ItemID (ItemID),
  KEY FromLocation (FromLocation),
  KEY ToLocation (ToLocation),
  CONSTRAINT transferorders_ibfk_1 FOREIGN KEY (ItemID) REFERENCES items (ItemID) ON DELETE SET NULL,
  CONSTRAINT transferorders_ibfk_2 FOREIGN KEY (FromLocation) REFERENCES location (LocationID) ON DELETE SET NULL,
  CONSTRAINT transferorders_ibfk_3 FOREIGN KEY (ToLocation) REFERENCES location (LocationID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferorders`
--

LOCK TABLES transferorders WRITE;
/*!40000 ALTER TABLE transferorders DISABLE KEYS */;
/*!40000 ALTER TABLE transferorders ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-17  0:48:41
