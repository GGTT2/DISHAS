<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use TAMAS\AstroBundle\DISHASToolbox\QueryGenerator;

class PublicSearchController extends Controller
{
    public function searchAction(Request $request)
    {
        $queryData = $request->request->get("query");
        if ($queryData){
            $queryData = json_decode($queryData,true);
            $query = json_encode($queryData["query"]);
            $queryTitle = $queryData["title"];
            $entity = $queryData["entity"];
        } else {
            $query = "";
            $queryTitle = "";
            $entity = "";
        }

        $fieldLists = [];
        $em = $this->getDoctrine()->getManager();
        $fieldLists["primary_source"] = $em->getRepository("TAMASAstroBundle:PrimarySource")->getPublicObjectList();
        $fieldLists["work"] = $em->getRepository("TAMASAstroBundle:Work")->getPublicObjectList();
        $fieldLists["original_text"] = $em->getRepository("TAMASAstroBundle:OriginalText")->getPublicObjectList();
        $fieldLists["edited_text"] = $em->getRepository("TAMASAstroBundle:EditedText")->getPublicObjectList();
        $fieldLists["parameter_set"] = $em->getRepository("TAMASAstroBundle:ParameterSet")->getPublicObjectList();
        $fieldLists["formula_definition"] = $em->getRepository("TAMASAstroBundle:FormulaDefinition")->getPublicObjectList();

        return $this->render('TAMASAstroBundle:PublicSearch:publicResult.html.twig', [
                'query' => $query,
                'entity' => $entity,
                'fieldLists' => $fieldLists,
                'queryTitle' => $queryTitle
            ]);
    }

    public function elasticQueryAction(Request $request)
    {
        $index = $request->query->get("index") ? $request->query->get("index")."/" : "";
        $query = $request->query->get("query") ?? "";
        $source = $request->query->get("source") ?? "[]";
        $from = $request->query->get("from") ?? 0;
        $onlyResults = $request->query->get("hits") ?? false;
        $isIdentified = $request->query->get("origin") == "intern";
        $size = $request->query->get("size");
        $id = $request->query->get("id") ?? 0;

        if (! $size){
            // default size queries are 10 hits for an unidentified request
            $size = $isIdentified ? 10000 : 10;
        } elseif ((!$isIdentified) && $size >= 200){
            // maximum size for an unidentified request is 200
            $size = 200;
        } elseif ($size >= 10000){
            // a elasticsearch request can't go above a size of 10000
            $size = 10000;
        }

        if ($index == ""){
            return JsonResponse::fromJsonString('{"response":"Please provide at least an index on which to perform the query"}');
        }
        $request = ["from" => $from, "size" => $size];

        if ($id != 0){
            $query = '{"match":{"id":'.$id.'}}';
        }

        if ($query != '""' && $query != ""){
            // replace all spaces because the encoding of spaces in URL causes the query to be malformed
            $request["query"] = json_decode(str_replace(" ", "%2B", $query), 1);
        }
        if ($source != "[]"){
            $request["_source"]= json_decode($source, 1);
        }

        $server =  $this->getParameter('elastic_host').":".$this->getParameter('elastic_port');
        $request = json_encode($request);

        $url = "$server/$index"."_search?source_content_type=application/json&source=$request";
        $jsonOutput = GenericTools::getWebpages($url)["output"];

        if ($onlyResults){
            $jsonOutput = QueryGenerator::retrieveResults($jsonOutput);
        }

        if ($id != 0){
            $jsonOutput = QueryGenerator::retrieveRecord($jsonOutput);
        }

        return JsonResponse::fromJsonString($jsonOutput);
    }
}
