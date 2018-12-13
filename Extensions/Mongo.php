<?php
	Class MongoDBase
	{
		static public function ConnectionToDB($dbName)
		{
			$mo = new MongoClient();
			echo "Connection to database successfully";
			$db = $mo->$dbName;
			echo "Database mimipapaba selected";
			return $db;
		}
		
		static public function CreateCollection($dbName,$collectionName)
		{
			$db = self::ConnectionToDB($dbName);
			$collection = $db->createCollection($collectionName);
			return $collection;
		}
		
		static public function InsertDocument($dbName,$collectionName,$documentName)
		{
			$collection = self::CreateCollection($dbName, $collectionName);
			$document = $collection->insert($documentName);
			return $document;
		}
		
		static public function SelectAll($dbName,$collectionName)
		{
			$collection = self::CreateCollection($dbName, $collectionName);
			return $cursor = $collection->find();
		}
		
		//static public function SelectBy
	}
	MongoDBase::ConnectionToDB();
?>