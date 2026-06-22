<?php

/*
|--------------------------------------------------------------------------
| Constitution content
|--------------------------------------------------------------------------
| PLACEHOLDER content. Replace the 'sections' array with the real
| constitution (or it will be regenerated from the supplied Word file).
| Each section: id (anchor slug), title, icon (feather name), body (HTML).
| The left nav, the article sections, and the right "On This Page" list
| are all generated from this array — no layout changes needed to swap content.
*/

return [
    'title' => 'Constitution',
    'subtitle' => 'Barakah Cooperative Association',
    'updated_at' => 'Draft — placeholder content',

    'sections' => [
        [
            'id' => 'preamble',
            'title' => 'Preamble',
            'icon' => 'book-open',
            'body' => <<<'HTML'
                <p>We, the members of <strong>Barakah Cooperative Association</strong>, in order to promote mutual
                economic welfare, encourage the habit of savings, and undertake lawful investments for the common
                benefit of our members, do hereby adopt and establish this Constitution.</p>
                <p>This document governs the formation, membership, management, and operation of the Association and
                shall be binding upon all members.</p>
HTML,
        ],
        [
            'id' => 'article-1-name',
            'title' => 'Article 1 — Name &amp; Registered Office',
            'icon' => 'home',
            'body' => <<<'HTML'
                <p><strong>1.1</strong> The name of the organization shall be the <strong>Barakah Cooperative
                Association</strong> (hereinafter referred to as "the Association").</p>
                <p><strong>1.2</strong> The registered office of the Association shall be located at the address
                recorded in the Organization Profile, and may be changed by resolution of the Executive Committee.</p>
                <p><strong>1.3</strong> The Association is a non-political, non-profit, member-owned body established
                for the economic and social benefit of its members.</p>
HTML,
        ],
        [
            'id' => 'article-2-definitions',
            'title' => 'Article 2 — Definitions',
            'icon' => 'list',
            'body' => <<<'HTML'
                <ul>
                    <li><strong>Member</strong> — any person admitted to membership under Article 4 and holding at
                    least one share.</li>
                    <li><strong>Share</strong> — a unit of capital of the Association having the face value fixed in
                    the Organization Profile.</li>
                    <li><strong>Committee</strong> — the Executive Committee elected to manage the Association.</li>
                    <li><strong>General Meeting</strong> — a meeting of all members convened under Article 7.</li>
                </ul>
HTML,
        ],
        [
            'id' => 'article-3-objectives',
            'title' => 'Article 3 — Objectives',
            'icon' => 'target',
            'body' => <<<'HTML'
                <p>The objectives of the Association are:</p>
                <ul>
                    <li><strong>Savings</strong> — to encourage regular monthly deposits among members.</li>
                    <li><strong>Investment</strong> — to invest the pooled funds of members in lawful, productive,
                    and ethically sound ventures.</li>
                    <li><strong>Welfare</strong> — to provide financial assistance and support to members in times of
                    need, subject to available funds.</li>
                    <li><strong>Transparency</strong> — to maintain accurate accounts and report regularly to members.</li>
                </ul>
HTML,
        ],
        [
            'id' => 'article-4-membership',
            'title' => 'Article 4 — Membership',
            'icon' => 'users',
            'body' => <<<'HTML'
                <p><strong>4.1 Eligibility.</strong> Membership is open to any individual of sound mind who agrees to
                abide by this Constitution and subscribes to at least one share.</p>
                <p><strong>4.2 Admission.</strong> Applications for membership shall be approved by the Executive
                Committee. Each member is assigned a unique member code upon admission.</p>
                <p><strong>4.3 Duties.</strong> Every member shall pay the prescribed membership fee and make monthly
                deposits as determined by their shareholding.</p>
                <p><strong>4.4 Cessation.</strong> Membership ends upon resignation, death, or expulsion by resolution
                for conduct injurious to the Association.</p>
HTML,
        ],
        [
            'id' => 'article-5-shares',
            'title' => 'Article 5 — Shares &amp; Capital',
            'icon' => 'pie-chart',
            'body' => <<<'HTML'
                <p><strong>5.1</strong> The capital of the Association is divided into shares of equal face value as
                recorded in the Organization Profile.</p>
                <p><strong>5.2</strong> A member's monthly deposit obligation is proportional to the number of shares
                held.</p>
                <p><strong>5.3</strong> Shares may be transferred between members only with the approval of the
                Executive Committee, in accordance with the share-transfer procedure.</p>
HTML,
        ],
        [
            'id' => 'article-6-funds',
            'title' => 'Article 6 — Funds &amp; Accounts',
            'icon' => 'dollar-sign',
            'body' => <<<'HTML'
                <p><strong>6.1</strong> All funds of the Association shall be deposited in accounts maintained in the
                name of the Association.</p>
                <p><strong>6.2</strong> Proper books of account shall be kept, and a statement of accounts shall be
                presented at every Annual General Meeting.</p>
                <p><strong>6.3</strong> Profits from investments, after deduction of expenses and any reserve, shall be
                distributed to members in proportion to their shareholding.</p>
HTML,
        ],
        [
            'id' => 'article-7-general-meeting',
            'title' => 'Article 7 — General Meeting',
            'icon' => 'calendar',
            'body' => <<<'HTML'
                <p><strong>7.1</strong> The Annual General Meeting (AGM) shall be held once each year.</p>
                <p><strong>7.2</strong> An Extraordinary General Meeting may be called by the Committee or upon written
                request of one-third of the members.</p>
                <p><strong>7.3</strong> Each member present shall have one vote. The quorum and notice period shall be
                as fixed in the Organization Profile.</p>
HTML,
        ],
        [
            'id' => 'article-8-committee',
            'title' => 'Article 8 — Executive Committee',
            'icon' => 'shield',
            'body' => <<<'HTML'
                <p><strong>8.1</strong> The affairs of the Association shall be managed by an Executive Committee
                elected by the members at the AGM.</p>
                <p><strong>8.2</strong> The Committee shall consist of office bearers including a President, Secretary,
                and Treasurer, holding office for the term fixed in the Organization Profile.</p>
                <p><strong>8.3</strong> The Committee is responsible for approving memberships, deposits, expenses,
                investments, and share transfers in accordance with this Constitution.</p>
HTML,
        ],
        [
            'id' => 'article-9-amendment',
            'title' => 'Article 9 — Amendment &amp; Dissolution',
            'icon' => 'edit-3',
            'body' => <<<'HTML'
                <p><strong>9.1 Amendment.</strong> This Constitution may be amended by a two-thirds majority of members
                present and voting at a General Meeting convened for that purpose.</p>
                <p><strong>9.2 Dissolution.</strong> The Association may be dissolved by a three-fourths majority of the
                total membership. Upon dissolution, after settling liabilities, remaining assets shall be distributed
                to members in proportion to their shareholding.</p>
HTML,
        ],
    ],
];
