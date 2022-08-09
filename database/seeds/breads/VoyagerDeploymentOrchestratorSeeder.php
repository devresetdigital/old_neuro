<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Traits\Seedable;

class VoyagerDeploymentOrchestratorSeeder extends Seeder
{
    use Seedable;

    protected $seedersPath = 'database/breads/seeds/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed(UsersBreadTypeAdded::class);
        $this->seed(UsersBreadRowAdded::class);
        $this->seed(CreativesBreadTypeAdded::class);
        $this->seed(CreativesBreadRowAdded::class);
        $this->seed(CampaignsBreadTypeAdded::class);
        $this->seed(CampaignsBreadRowAdded::class);
        $this->seed(StrategiesBreadTypeAdded::class);
        $this->seed(StrategiesBreadRowAdded::class);
        $this->seed(PixelsBreadTypeAdded::class);
        $this->seed(PixelsBreadRowAdded::class);
        $this->seed(AttributionReportBreadTypeAdded::class);
        $this->seed(AttributionReportBreadRowAdded::class);
        $this->seed(OrganizationsBreadTypeAdded::class);
        $this->seed(OrganizationsBreadRowAdded::class);
        $this->seed(ConceptsBreadTypeAdded::class);
        $this->seed(ConceptsBreadRowAdded::class);
        $this->seed(AdvertisersBreadTypeAdded::class);
        $this->seed(AdvertisersBreadRowAdded::class);
        $this->seed(CustomDatasBreadTypeAdded::class);
        $this->seed(CustomDatasBreadRowAdded::class);
        $this->seed(SitelistsBreadTypeAdded::class);
        $this->seed(SitelistsBreadRowAdded::class);
        $this->seed(PublisherlistsBreadTypeAdded::class);
        $this->seed(PublisherlistsBreadRowAdded::class);
        $this->seed(KeywordslistsBreadTypeAdded::class);
        $this->seed(KeywordslistsBreadRowAdded::class);
        $this->seed(ZiplistsBreadTypeAdded::class);
        $this->seed(ZiplistsBreadRowAdded::class);
        $this->seed(BlocklistsBreadTypeAdded::class);
        $this->seed(BlocklistsBreadRowAdded::class);
        $this->seed(IplistsBreadTypeAdded::class);
        $this->seed(IplistsBreadRowAdded::class);
        $this->seed(PmpsBreadTypeAdded::class);
        $this->seed(PmpsBreadRowAdded::class);
    }
}
