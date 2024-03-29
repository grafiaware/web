

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[RebuildNestedSets_MM] AS
/****************************************************************************
 Purpose:
 Rebuilds a "Hierarchy" table that contains the original Adjacency List,
 the Nested Sets version of the same hierarchy, and several other useful
 columns of data some of which need not be included in the final table.

 Usage:
 EXEC dbo.RebuildNestedSets_MM

 Progammer's Notes:
 1. As currently written, the code reads from a table called dbo.Employee.
 2. The Employee table must contain well indexed EmployeeID (child) and
    ManagerID (parent) columns.
 3. The Employee table must be a "well formed" Adjacency List. That is, the
    EmployeeID column must be unique and there must be a foreign key on the
    ManagerID column that points to the EmployeeID column. The table must not
    contain any "cycles" (an EmployeeID in its own upline). The Root Node
    must have a NULL for ManagerID.
 4. The final table, named dbo.Hierarchy, will be created in the same
    database as where this stored procedure is present.  IT DOES DROP THE
    TABLE CALLED DBO.HIERARCHY SO BE CAREFUL THAT IT DOESN'T DROP A TABLE
    NEAR AND DEAR TO YOUR HEART.
 5. This code currently has no ROLLBACK capabilities so make sure that you
    have met all of the requirements (and, perhaps, more) cited in #3 above.

 Dependencies:
 1. This stored procedure requires that the following special purpose HTally
    table be present in the same database from which it runs.

--===== Create the HTally table to be used for splitting SortPath
 SELECT TOP 1000 --(4 * 1000 = VARBINARY(4000) in length)
        N = ISNULL(CAST(
                (ROW_NUMBER() OVER (ORDER BY (SELECT NULL))-1)*4+1
            AS INT),0)
   INTO dbo.HTally
   FROM master.sys.all_columns ac1
  CROSS JOIN master.sys.all_columns ac2
;
--===== Add the quintessential PK for performance.
  ALTER TABLE dbo.HTally
    ADD CONSTRAINT PK_HTally
        PRIMARY KEY CLUSTERED (N) WITH FILLFACTOR = 100
;

 Revision History:
 Rev 00 - Circa 2009  - Jeff Moden
        - Initial concept and creation.
 Rev 01 - PASS 2010   - Jeff Moden
        - Rewritten for presentation at PASS 2010.
 Rev 02 - 06 Oct 2012 - Jeff Moden
        - Code redacted to include a more efficient, higher performmance
          method of splitting the SortPath using a custom HTally Table.
 Sug 00 - 13 NOv 2012 - Mister Magoo
      - CTE replaced with Magoo's Identity Hack for a more efficient recursion
      - Also removes the need to tweak MAXRECURSION
****************************************************************************/
--===========================================================================
--      Presets
--===========================================================================
--===== Suppress the auto-display of rowcounts to prevent from returning
     -- false errors if called from a GUI or other application.
    SET NOCOUNT ON;

--===== Start a duration timer
DECLARE @StartTime DATETIME,
        @Duration  CHAR(12);
 SELECT @StartTime = GETDATE();

--===========================================================================
--      1.  Read ALL the nodes in a given level as indicated by the parent/
--          child relationship in the Adjacency List.
--      2.  As we read the nodes in a given level, mark each node with the
--          current level number.
--      3.  As we read the nodes in a given level, convert the EmployeeID to
--          a Binary(4) and concatenate it with the parents in the previous
--          level's binary string of EmployeeID's.  This will build the
--          SortPath.
--      4.  Number the rows according to the Sort Path.  This will number the
--          rows in the same order that the push-stack method would number
--          them.
--===========================================================================
--===== Conditionally drop the final table to make reruns easier in SSMS.
     IF OBJECT_ID('FK_Hierarchy_Hierarchy') IS NOT NULL
        ALTER TABLE dbo.Hierarchy
         DROP CONSTRAINT FK_Hierarchy_Hierarchy;

     IF OBJECT_ID('dbo.Hierarchy','U') IS NOT NULL
         DROP TABLE dbo.Hierarchy;

RAISERROR('Building the initial table and SortPath...',0,1) WITH NOWAIT;

--===== Mister Magoo Mods Start

--===== Build the tmep table for the Identity Hack

CREATE TABLE #BuildPath(
   EmployeeID   INT NOT NULL ,
   ManagerID   INT,
   HLevel      SMALLINT IDENTITY(1,1) NOT NULL,
   LeftBower   INT NOT NULL,
   RightBower   INT NOT NULL,
   NodeNumber   INT NOT NULL,
   NodeCount   INT NOT NULL,
   SortPath   VARBINARY(4000)
);

--===== Add a nice index to help with selecting the levels for recursion.
CREATE CLUSTERED INDEX ix_cl ON #BuildPath(HLevel);

--===== Turn on identity insert so that we can put our own values into the HLevel column.
SET IDENTITY_INSERT #BuildPath ON;

--===== Insert the top level Manager(s) into the temp table
INSERT #BuildPath(
   EmployeeID ,
   ManagerID ,
   HLevel ,
   LeftBower,
   RightBower,
   NodeNumber,
   NodeCount,
   SortPath )
SELECT anchor.EmployeeID,
    anchor.ManagerID,
    HLevel   = 1,
   LeftBower = 0,
   RightBower = 0,
   NodeNumber = 0,
   NodeCount = 0,
    SortPath =  CAST(
                    CAST(anchor.EmployeeID AS BINARY(4))
                AS VARBINARY(4000)) --Up to 1000 levels deep.
FROM dbo.Employee AS anchor
WHERE ManagerID IS NULL; --Only the Root Node has a NULL ManagerID

 --==== This is the "recursive" part of the Identity Hack that adds 1 for each level
     -- and concatenates each level of EmployeeID's to the SortPath column.
    -- It uses SCOPE_IDENTITY to "read" the last level processed.
WHILE @@ROWCOUNT>0
 INSERT #BuildPath(
    EmployeeID ,
   ManagerID ,
   HLevel ,
   LeftBower,
   RightBower,
   NodeNumber,
   NodeCount,
   SortPath )
 SELECT recur.EmployeeID,
        recur.ManagerID,
        HLevel   =  SCOPE_IDENTITY() + 1,
      LeftBower = 0,
      RightBower = 0,
      NodeNumber = 0,
      NodeCount = 0,
        SortPath =  CAST( --This does the concatenation to build SortPath
                        cte.SortPath + CAST(Recur.EmployeeID AS BINARY(4))
                    AS VARBINARY(4000))
   FROM dbo.Employee      AS recur WITH (TABLOCK)
   JOIN #BuildPath as cte WITH(TABLOCK)
   ON cte.EmployeeID = recur.ManagerID
   WHERE cte.HLevel=SCOPE_IDENTITY();

SET IDENTITY_INSERT #BuildPath OFF;

 --=== This final INSERT/SELECT creates the Node # in the same order as a
     -- push-stack would. It also creates the final table with some
     -- "reserved" columns on the fly. We'll leave the SortPath column in
     -- place because we're still going to need it later.
     -- The ISNULLs make NOT NULL columns
 SELECT EmployeeID = sorted.EmployeeID,
        sorted.ManagerID,
        HLevel     = sorted.HLevel,
        LeftBower  , --Place holder
        RightBower , --Place holder
        NodeNumber = ROW_NUMBER() OVER (ORDER BY sorted.SortPath),
        NodeCount  , --Place holder
        SortPath   = sorted.SortPath
   INTO dbo.Hierarchy
   FROM #BuildPath AS sorted;

RAISERROR('There are %u rows in dbo.Hierarchy',0,1,@@ROWCOUNT) WITH NOWAIT;

--===== Mister Magoo Mods End

--===== Display the cumulative duration
 SELECT @Duration = CONVERT(CHAR(12),GETDATE()-@StartTime,114);
RAISERROR('Cumulative Duration = %s',0,1,@Duration) WITH NOWAIT;

--===========================================================================
--      Using the information created in the table above, create the
--      NodeCount column and the LeftBower and RightBower columns to create
--      the Nested Sets hierarchical structure.
--===========================================================================
RAISERROR('Building the Nested Sets...',0,1) WITH NOWAIT;

--===== Declare a working variable to hold the result of the calculation
     -- of the LeftBower so that it may be easily used to create the
     -- RightBower in a single scan of the final table.
DECLARE @LeftBower INT
;
--===== Create the Nested Sets from the information available in the table
     -- and in the following CTE. This uses the proprietary form of UPDATE
     -- available in SQL Serrver for extra performance.
   WITH cteCountDownlines AS
( --=== Count each occurance of EmployeeID in the sort path
 SELECT EmployeeID = CAST(SUBSTRING(h.SortPath,t.N,4) AS INT),
        NodeCount  = COUNT(*) --Includes current node
   FROM dbo.Hierarchy h,
        dbo.HTally t
  WHERE t.N BETWEEN 1 AND DATALENGTH(SortPath)
  GROUP BY SUBSTRING(h.SortPath,t.N,4)
) --=== Update the NodeCount and calculate both Bowers
 UPDATE h
    SET @LeftBower   = LeftBower = 2 * NodeNumber - HLevel,
        h.NodeCount  = downline.NodeCount,
        h.RightBower = (downline.NodeCount - 1) * 2 + @LeftBower + 1
   FROM dbo.Hierarchy h
   JOIN cteCountDownlines downline
     ON h.EmployeeID = downline.EmployeeID
;
RAISERROR('%u rows have been updated to Nested Sets',0,1,@@ROWCOUNT)
WITH NOWAIT;

RAISERROR('If the rowcounts don''t match, there may be orphans.'
,0,1,@@ROWCOUNT)WITH NOWAIT;

--===== Display the cumulative duration
 SELECT @Duration = CONVERT(CHAR(12),GETDATE()-@StartTime,114);
RAISERROR('Cumulative Duration = %s',0,1,@Duration) WITH NOWAIT;

--===========================================================================
--      Prepare the table for high performance reads by adding indexes.
--===========================================================================
RAISERROR('Building the indexes...',0,1) WITH NOWAIT;

--===== Direct support for the Nested Sets
  ALTER TABLE dbo.Hierarchy
    ADD CONSTRAINT PK_Hierarchy
        PRIMARY KEY CLUSTERED (LeftBower, RightBower) WITH FILLFACTOR = 100
;
 CREATE UNIQUE INDEX AK_Hierarchy
     ON dbo.Hierarchy (EmployeeID) WITH FILLFACTOR = 100
;
  ALTER TABLE dbo.Hierarchy
    ADD CONSTRAINT FK_Hierarchy_Hierarchy FOREIGN KEY
        (ManagerID) REFERENCES dbo.Hierarchy (EmployeeID)
     ON UPDATE NO ACTION
     ON DELETE NO ACTION
;
--===== Display the cumulative duration
 SELECT @Duration = CONVERT(CHAR(12),GETDATE()-@StartTime,114);
RAISERROR('Cumulative Duration = %s',0,1,@Duration) WITH NOWAIT;

--===========================================================================
--      Exit
--===========================================================================
RAISERROR('===============================================',0,1) WITH NOWAIT;
RAISERROR('RUN COMPLETE',0,1) WITH NOWAIT;
RAISERROR('===============================================',0,1) WITH NOWAIT;


