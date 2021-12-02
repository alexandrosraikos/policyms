<?php

class PolicyCloud_Marketplace_Review
{
    protected PolicyCloud_Marketplace_Description $description;

    public string $comment;

    public int $rating;


    public function __construct(PolicyCloud_Marketplace_Description $description, array $data = null)
    {
        $this->description = $description;

        if (empty($data)) {
        }
    }
}
