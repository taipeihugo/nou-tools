<?php

use App\Models\Announcement;
use Illuminate\Support\Facades\Http;
use NouTools\Domains\Announcements\Actions\SyncAnnouncements;
use NouTools\Domains\Announcements\DataTransferObjects\AnnouncementSourceConfigDTO;
use NouTools\Domains\Announcements\Fetchers\SchoolHpFetcher;

function schoolHpSourceConfig(array $overrides = []): AnnouncementSourceConfigDTO
{
    return AnnouncementSourceConfigDTO::fromConfig(
        $overrides['key'] ?? 'school-homepage-source',
        array_merge([
            'name' => '學校首頁',
            'category' => '最新消息',
            'fetch_url' => 'https://www.nou.edu.tw/news_idx1.aspx?id=K3JOsteln/k=',
            'fetcher_type' => 'school_hp',
            'fetcher_config' => ['base_url' => 'https://www.nou.edu.tw'],
            'tracks_expiry' => false,
            'is_active' => true,
        ], $overrides),
    );
}

it('parses list-group items across tab 0 to 6 and strips tag text', function () {
    $source = schoolHpSourceConfig();

    $html = <<<'HTML'
    <html><body>
    <div id="cph_Content_rt_Content_dv_Name_0" class="tab-pane fade in active">
        <div class="list-group">
            <a class="list-group-item" href="/news_cont.aspx?id=A1=">2026/03/11 <span class="w3-tag w3-teal">Top</span> 校本部配合每半年高壓電用電設備保養維護停電公告 / 總務處營繕組</a>
            <a class="list-group-item" href="/news_cont.aspx?id=A2=">2026/05/02  數位學習平台伺服(uu.nou.edu.tw)異常</a>
        </div>
    </div>

    <div id="cph_Content_rt_Content_dv_Name_6" class="tab-pane fade">
        <div class="list-group">
            <a class="list-group-item" href="/news_cont.aspx?id=A3=">2026/04/27  國立空中大學教學媒體處行政組員(大學級)徵才公告 / 人事室</a>
        </div>
    </div>

    <div id="cph_Content_rt_Content_dv_Name_7" class="tab-pane fade">
        <div class="list-group">
            <a class="list-group-item" href="/news_cont.aspx?id=OUT=">2026/01/01  這筆不該被抓到</a>
        </div>
    </div>
    </body></html>
    HTML;

    Http::fake([
        'www.nou.edu.tw/*' => Http::response($html),
    ]);

    $fetcher = new SchoolHpFetcher;
    $results = $fetcher->fetch($source);

    expect($results)->toHaveCount(3)
        ->and($results[0]->sourceId)->toBe('/news_cont.aspx?id=A1=')
        ->and($results[0]->title)->toBe('校本部配合每半年高壓電用電設備保養維護停電公告 / 總務處營繕組')
        ->and($results[0]->url)->toBe('https://www.nou.edu.tw/news_cont.aspx?id=A1=')
        ->and($results[0]->tags)->toBeNull()
        ->and($results[0]->publishedAt?->format('Y-m-d'))->toBe('2026-03-11')
        ->and($results[1]->title)->toBe('數位學習平台伺服(uu.nou.edu.tw)異常')
        ->and($results[2]->sourceId)->toBe('/news_cont.aspx?id=A3=');
});

it('skips list-group items without date prefix, href, or usable title', function () {
    $source = schoolHpSourceConfig([
        'fetch_url' => 'https://example.com/news_idx1.aspx',
        'fetcher_config' => ['base_url' => 'https://example.com'],
    ]);

    $html = <<<'HTML'
    <html><body>
    <div id="cph_Content_rt_Content_dv_Name_0">
        <div class="list-group">
            <a class="list-group-item" href="">2026/05/02 沒有連結</a>
            <a class="list-group-item" href="/news_cont.aspx?id=1">這筆沒有日期前綴</a>
            <a class="list-group-item" href="/news_cont.aspx?id=2">2026/05/02 <span class="w3-tag w3-teal">Top</span></a>
            <a class="list-group-item" href="/news_cont.aspx?id=3">2026/05/02 有效公告</a>
        </div>
    </div>
    </body></html>
    HTML;

    Http::fake([
        'example.com/*' => Http::response($html),
    ]);

    $fetcher = new SchoolHpFetcher;
    $results = $fetcher->fetch($source);

    expect($results)->toHaveCount(1)
        ->and($results[0]->sourceId)->toBe('/news_cont.aspx?id=3')
        ->and($results[0]->title)->toBe('有效公告')
        ->and($results[0]->url)->toBe('https://example.com/news_cont.aspx?id=3');
});

it('syncs school homepage announcements', function () {
    $source = schoolHpSourceConfig();

    $html = <<<'HTML'
    <html><body>
    <div id="cph_Content_rt_Content_dv_Name_2">
        <div class="list-group">
            <a class="list-group-item" href="/news_cont.aspx?id=SYNC=">2026/04/28 <span class="w3-tag w3-teal">Top</span> 115年國立空中大學徵聘特殊教育資源中心輔導人員</a>
        </div>
    </div>
    </body></html>
    HTML;

    Http::fake([
        'www.nou.edu.tw/*' => Http::response($html),
    ]);

    $syncAction = app(SyncAnnouncements::class);
    $newCount = $syncAction($source);

    expect($newCount)->toBe(1)
        ->and(Announcement::query()->count())->toBe(1);

    $announcement = Announcement::query()->first();
    expect($announcement)->not->toBeNull()
        ->and($announcement->source_key)->toBe($source->key)
        ->and($announcement->source_name)->toBe($source->name)
        ->and($announcement->category)->toBe($source->category)
        ->and($announcement->source_id)->toBe('/news_cont.aspx?id=SYNC=')
        ->and($announcement->title)->toBe('115年國立空中大學徵聘特殊教育資源中心輔導人員')
        ->and($announcement->url)->toBe('https://www.nou.edu.tw/news_cont.aspx?id=SYNC=')
        ->and($announcement->tags)->toBeNull()
        ->and($announcement->published_at?->format('Y-m-d'))->toBe('2026-04-28');
});
