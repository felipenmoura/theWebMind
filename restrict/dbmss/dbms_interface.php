<?php
	interface dbmsInterface
	{
		public function query($dbCon, $qr);					// executes a query into the dataBase
		public function connectTo($dbObject);				// OPENS a connection to the dataBase
		public function disconnectFrom($connectionObject);	// CLOSES the connection to the dataBase
		public function getLastError($dbCon);				// returns the last occurred error
		public function tableExists($conObj, $table);		// returns false if the table doesn't exist
		
		public function getHeader();						// returns the header, like comments on top, possible variables or calls to select dataBase
		public function establishConnection();				// returns the STRING needed to the connection
		public function closeConnection();					// returns the STRING needed to the disconnection
		public function fetchArray();						// returns the STRING of the command used to fetch the result into an Array
		public function lastError();						// returns the STRING of the command used to get the lest error
		public function addField();							// returns the STRING of the command used to add fields
		public function dropTable();						// returns the STRING of the command used to drop a table
		public function dropField();						// returns the STRING of the command used to remove a field from a table
		public function queryCommand();						// returns the STRING of the command used to exec a query
		public function createFieldComment();				// returns the STRING of the command used to add a comment on fields
		public function addFK();							// returns the STRING of the command used to add a foreignkey to a table
		public function createPK();							// returns the STRING of the command used to specify the primary key
		public function createTable();						// returns the STRING of the command used to create a table
		public function createField();						// returns the STRING of the command used to create a field into the createTable command
		public function setDefaultValue();					// ???
		public function defaultPrimaryKey();				// ???
	}
?>