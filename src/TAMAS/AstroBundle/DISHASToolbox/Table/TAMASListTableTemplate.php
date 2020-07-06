<?php
namespace TAMAS\AstroBundle\DISHASToolbox\Table;

/**
 * This class create spec object for the column of each list of entity.
 * It is used in the repository method listObject().
 *
 */
class TAMASListTableTemplate
{

    /**
     * Name of the column
     * Match the keys of the object to be displayed.
     * Ex : editedText has a key "authors" ; the name will be authors.
     *
     * When performing a query to elasticsearch, this name is either the name of the field
     * in which the information is stored
     * EX : "id" to retrieve the id of a record
     *
     * Or the name of a key that is artificially created in the result object and where the
     * formatted data will be stored
     * EX : "table_editions" to put formatted table edition list
     * when the information is located "table_contents.edited_text.edited_text_title"
     *
     * @var {string}
     */
    public $name;

    /**
     * Displayed name of the column
     * Ex : editedText has a key "authors" ; the title will be "Author(s)".
     *
     * @var {string}
     */
    public $title;

    /**
     * Name of the field when queried in Elasticsearch
     * Ex : editedText is associated with an historian ;
     * to get his first name, the source will be "historian.first_name"
     *
     * Needs to be specified only if different from name
     *
     * @var {string}
     */
    public $source;

    /**
     * Restrict usage of this column (e.g.: ifOnly "adminInfo" : this column only displayed if if only ="adminInfo")
     *
     * @var {string}
     */
    public $ifOnly;

    /**
     * Properties of all the columns
     *
     * The properties defines how the cell content will be formatted :
     * 		- class (will surround the text in a <span class="__"></span>) :
     * 			* number : in order to align the text to the right
     * 			* title-italic : to style the text of the cell in italic
     *          * uppercase : to style the text of the cell in uppercase letter
     * 		- path + id :
     * 			* path : routing path to generate a link
     * 			* id : location of the id in the result object
     * 		- unknown : text to display if there is no information provided in the results)
     *
     * [
     *      "class" => ["nameOfClass1","nameOfClass2"],
     *      "path" => "tamas_astro_viewEntity",
     *      "id" => "where.to.get.id",
     *      "unknown" => "text to display in case no information is provided"
     * ]
     *
     * @var {array}
     */
    public $properties;

    /**
     * Defined if the information is going to be displayed in the datatable
     * @var bool
     */
    public $displayInTable;


    public function __construct(string $name, string $title, array $properties=[], string $ifOnly = null, string $source="", bool $displayInTable=true)
    {
        $this->name = $name;
        $this->title = $title;
        $this->ifOnly = $ifOnly;
        $this->properties = $properties;
        $this->source = $source;
        $this->displayInTable = $displayInTable;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Generates an array of field names used as source (specifying the fields that will appear in the results)
     * in an elasticsearch query
     *
     * EX : $fields = ["id","default_title","tpq","taq","historical_actors.actor_name"];
     *
     * @param array $fieldList (filled with TAMASListTableTemplate objects)
     *
     * @param array $fieldList
     * @return false|string
     */
    public static function getSources(array $fieldList){
        $sources = [];
        $list = new TAMASListTableTemplate('id', '#');
        $list->source;

        foreach ($fieldList as $field){
            if ($field->getSource()){
                $sources[] = $field->getSource();
            } else {
                $sources[] = $field->getName();
            }
        }

        return json_encode($sources);
    }

}


