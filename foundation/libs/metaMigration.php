<?php

/* 
 * Projects4Me Community Edition is an open source project management software 
 * developed by PROJECTS4ME Inc. Copyright (C) 2015-2016 PROJECTS4ME Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 (GNU AGPL v3) as
 * published be the Free Software Foundation with the addition of the following 
 * permission added to Section 15 as permitted in Section 7(a): FOR ANY PART OF 
 * THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY PROJECTS4ME Inc., 
 * Projects4Me DISCLAIMS THE WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU AGPL v3 for more details.
 * 
 * You should have received a copy of the GNU AGPL v3 along with this program; 
 * if not, see http://www.gnu.org/licenses or write to the Free Software 
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 * 
 * You can contact PROJECTS4ME, Inc. at email address contact@projects4.me.
 * 
 * The interactive user interfaces in modified source and object code versions 
 * of this program must display Appropriate Legal Notices, as required under 
 * Section 5 of the GNU AGPL v3.
 * 
 * In accordance with Section 7(b) of the GNU AGPL v3, these Appropriate Legal 
 * Notices must retain the display of the "Powered by Projects4Me" logo. If the 
 * display of the logo is not reasonably feasible for technical reasons, the 
 * Appropriate Legal Notices must display the words "Powered by Projects4Me".
 */

namespace Foundation\Mvc\Model;

// Since this class is consumed through the command line we need to include the
// required classes
require_once APP_PATH.'/foundation/libs/fileHandler.php';
require_once APP_PATH.'/foundation/libs/metaManager.php';

use Phalcon\Mvc\Model\Migration as PhalconMigration;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;

use Foundation\fileHandler;
use Foundation\metaManager;

/**
 * This class is responsible for synchronizing the database by using the
 * metadata defined for all the models in app\metadata\model.
 * 
 * This calss is dependant on Phalcon Dev Tools. This class cannot be called
 * through the user interface yet as the files required are to be seperately
 * install on the system 
 * 
 * Currently with Phalcon 2.0.6 the following syntax can be used to sync the
 * data 
 * 
 *  $ phalcon migration run
 * 
 * It is advised that you do not run 
 * 
 *  $ phalcon migration generate 
 * 
 * This command would override all the files in the app\migrations and have them
 * use Phalcon\Mvc\Model\Migration instead of \Foundation\Mvc\Model\Migration
 *  
 * @author Hammad Hassan <gollomer@gmail.com>
 * @package Foundation\Mvc\Model
 * @category Migration
 * @license http://www.gnu.org/licenses/agpl.html AGPLv3
 */
class Migration extends PhalconMigration
{
    /**
     * This function is responsible for synchronizing the database using the
     * metadata. As per the Phalcon Dev Tools this function is to be implemented
     * in the individual files in app\migrations. However, since in this
     * application we are relying on model metadata defined through files in
     * app\metadata\model it would be redundant to have the columns defined
     * again so this function get the metadata from the files and sets them up
     * 
     * @todo get table engines and collation from the model meta and system config
     */
    public function up()
    {
        // Get the entity name from the Mirgation files
        $entity = $this->getModelName();
        
        // Get the table definitions from the meta data
        $tableDefinition = $this->getMetaData($entity);
        
        // Sync the table in the database
        $this->morphTable(
            $tableDefinition['tableName'],
            array(
                'columns' => $tableDefinition['columns'],
                'indexes' => $tableDefinition['indexes'],
                'options' => array(
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_unicode_ci'
                )
            )
        );
    }
    
    /**
     * This function is responsible for reading the individual metadata from
     * the application and process it for this class
     * 
     * @todo Include parsing for all type of indexes
     * @param string $model
     * @return array
     */
    private function getMetaData($model)
    {
        // Read the metadata from the file
        $meta = fileHandler::readFile(APP_PATH.metaManager::basePath.'/model/'.$model.'.php');
        
        // Initialize the array to be filled in
        $tableDescription = array(
            'tableName' => $meta[$model]['tableName'],
            'columns' => array(),
            'indexes' => array(),
        );
        
        // Traverse through the fields and process them
	foreach($meta[$model]['fields'] as $field => $schema)
	{
            $fieldOptions = array();
            $fieldOptions['type'] = metaManager::getFieldType($schema['type']);
            $fieldOptions['size'] = $schema['length'];
            $fieldOptions['notNull'] = !$schema['null'];
            $tableDescription['columns'][] = new Column($schema['name'],$fieldOptions);
	}

        // Traverse through the indexes and process them
        foreach($meta[$model]['indexes'] as $field => $type)
	{
            // need to be able to recognize all types of indexes
            $index = '';
            if ($type == 'primary')
            {
                $index = 'PRIMARY';
            }
            else if ($type == 'unique')
            {
                $index = $field.'_UNIQUE';			
            }
            $tableDescription['indexes'][] = new Index($index, array($field));
	}
        
        return $tableDescription;
    }
    
    /**
     * Parse the class name to get the model name
     * 
     * @return string
     */
    private function getModelName()
    {
        return preg_replace('/Migration_.*/', '', get_class($this));
    }
}