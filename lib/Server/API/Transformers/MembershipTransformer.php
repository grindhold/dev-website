<?php

namespace OParl\Server\API\Transformers;

use OParl\Server\Model\Membership;

class MembershipTransformer extends BaseTransformer
{
    public function transform(Membership $membership)
    {
        $data = array_merge($this->getDefaultAttributesForEntity($membership), [
            'organization' => route('api.v1.organization.show', $membership->organization),
            'role'         => $membership->role,
            'votingRight'  => $membership->voting_right,
            'startDate'    => $this->formatDate($membership->start_date),
            'endDate'      => $this->formatDate($membership->end_date),
            // TODO: on_behalf_of
        ]);

        if (!$this->isIncluded()) {
            $data['person'] = route('api.v1.person.show', $membership->person);
        }

        return $this->cleanupData($data, $membership);
    }
}
