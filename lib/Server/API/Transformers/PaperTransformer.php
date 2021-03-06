<?php

namespace OParl\Server\API\Transformers;

use OParl\Server\Model\Paper;

class PaperTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['location', 'consultation'];

    public function transform(Paper $paper)
    {
        $data = array_merge($this->getDefaultAttributesForEntity($paper), [
            'body' => route('api.v1.body.show', $paper->body),
            'name' => $paper->name,
            'reference' => $paper->reference,
            'date' => $this->formatDate($paper->date),
            'paperType' => $paper->paper_type,
            'relatedPaper' => $this->collectionRouteList('api.v1.paper.show', $paper->relatedPapers),
            'subordinatedPaper' => $this->collectionRouteList('api.v1.paper.show', $paper->subordinatedPapers),
            'superordinatedPaper' => $this->collectionRouteList('api.v1.paper.show', $paper->superordinatedPapers),
            'mainFile' => $paper->mainFile,
            'auxiliaryFile' => $this->collectionRouteList('api.v1.file.show', $paper->auxiliaryFiles),
            'originatorPerson' => $this->collectionRouteList('api.v1.person.show', $paper->originatorPeople),
            'underDirectionOf' => $this->collectionRouteList('api.v1.organization.show', $paper->underDirectionOfOrganizations),
            'originatorOrganization' => $this->collectionRouteList('api.v1.organization.show', $paper->originatorOrganizations),
        ]);

        return $this->cleanupData($data, $paper);
    }

    public function includeLocation(Paper $paper) {
        return $this->collection($paper->locations, new LocationTransformer(true), 'included');
    }

    public function includeConsultation(Paper $paper) {
        return $this->collection($paper->consultations, new ConsultationTransformer(true), 'included');
    }
}
