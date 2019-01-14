<?php
namespace Ydalbj\LaravelUtils\Helpers;

use DB;

class DBHelper
{
    /*
     * ----------------------------------
     * update batch
     * ----------------------------------
     *
     * multiple update in one query
     *
     * tablename( required | string )
     * multipleData ( required | array of array ) 只存储可信数据
     */
    public static function updateBatch(string $tableName = "", array $multipleData = [])
    {
        if ($tableName && !empty($multipleData)) {
            // column or fields to update
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ($updateColumn as $uColumn) {
                $q .=  $uColumn." = CASE ";

                foreach ($multipleData as $data) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach ($multipleData as $data) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";

            #Log::info("更新数据库updateBatch：{$q}");
            // Update
            return DB::update(DB::raw($q));
        } else {
            return false;
        }
    }
}
