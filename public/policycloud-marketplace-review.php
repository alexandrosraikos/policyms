<?php 

class PolicyCloud_Marketplace_Review {
    protected PolicyCloud_Marketplace_Description $description;

    public string $comment;

    public int $rating;


    public function __construct(PolicyCloud_Marketplace_Description $description, array $data = null)
    {  
        $this->description = $description;

        if(empty($data)) {

        }
    }

    protected static function match_fields() {

    }

    protected function fetch_reviews(PolicyCloud_Marketplace_Description|PolicyCloud_Marketplace_User $description) {

    }

    public static function create(PolicyCloud_Marketplace_User $user, PolicyCloud_Marketplace_Description $description) {
        
    }
}