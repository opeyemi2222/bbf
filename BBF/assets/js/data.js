/**
 * BBF DATA LAYER
 * ============================================================
 * All mock data for Brigade Brotherhood Fellowship website.
 * This file is the single source of truth for all content.
 *
 * SOURCE OF TRUTH: BBF Constitution (adopted by founding members)
 *
 * BACKEND INTEGRATION NOTE:
 * When a backend is available, replace each data object/array
 * with an API fetch call to the corresponding endpoint.
 * All render functions in main.js, events.js, gallery.js, etc.
 * consume data from this file — no HTML needs to change.
 * ============================================================
 */

'use strict';

// ─── ORGANIZATION INFO ────────────────────────────────────────────────────────
// Source: BBF Constitution, Articles 1, 2, 3

const BBF_INFO = {
  name:         'Brigade Brotherhood Fellowship',
  shortName:    'BBF',
  // Official motto — BBF Constitution, Article 1.3
  motto:        'ONE BRIGADE • ONE BROTHERHOOD • ONE PURPOSE',
  // Official guiding values — BBF Constitution, Article 1.4
  guidingValues:'FAITH • FELLOWSHIP • SERVICE • FRIENDSHIP',
  // Biblical foundation — BBF Constitution, Article 2.3
  bibleVerse:   'Behold, how good and pleasant it is when brothers dwell in unity! — Psalm 133:1',
  // Summary identity statement — BBF Constitution, Article 3.10
  identityStatement: 'From the Brigade, we build Brotherhood; through Fellowship, we grow together.',
  // BBF is specifically a Boys\' Brigade fellowship — Art. 2.2
  brigadeBody:  "The Boys' Brigade",
  founded:      null, // Date of adoption to be filled at formal adoption — Art. 25
  email:        'info@bbfellowship.org',
  phone:        '',   // To be confirmed by founding officers
  address:      '',   // To be confirmed by founding officers
  socialMedia: {
    facebook:   '#',
    twitter:    '#',
    instagram:  '#',
    youtube:    '#',
    whatsapp:   '#',
  },
};

// ─── STATS / PRESENCE COUNTERS ────────────────────────────────────────────────
// NOTE: All figures below are SAMPLE/PLACEHOLDER data only.
// TODO [BACKEND]: Replace with GET /api/stats — live figures from membership database.

const BBF_STATS = [
  { id: 'stat-members',    label: 'Registered Members',  value: 500,  suffix: '+', icon: 'fa-users'            },
  { id: 'stat-chapters',   label: 'Active Chapters',     value: 20,   suffix: '+', icon: 'fa-building-user'    },
  { id: 'stat-activities', label: 'Activities Organised', value: 50,  suffix: '+', icon: 'fa-hands-helping'    },
  { id: 'stat-years',      label: 'Years of Fellowship', value: 1,    suffix: '',  icon: 'fa-shield-halved'    },
];

// ─── GUIDING VALUES ───────────────────────────────────────────────────────────
// Source: BBF Constitution, Articles 1.4, 2.1, 3.9
// These are the FOUR official guiding values: Faith, Fellowship, Service, Friendship.
// Additional identity values from Article 1.2 are included as supporting items.

const BBF_VALUES = [
  {
    icon: 'fa-cross',
    title: 'Faith',
    description: 'Christ remains our foundation. BBF operates on the foundation of Christian faith and encourages members to demonstrate Christian character in all their conduct and relationships.',
    official: true, // official guiding value per Art. 1.4
  },
  {
    icon: 'fa-people-roof',
    title: 'Fellowship',
    description: 'We come together and grow together. BBF is the platform through which members interact, socialise, share experiences, support one another, and build meaningful relationships.',
    official: true,
  },
  {
    icon: 'fa-hands-helping',
    title: 'Service',
    description: 'We use our abilities to serve God, The Boys\' Brigade, and society. Members are encouraged to contribute positively to the Church, their communities, and the nation.',
    official: true,
  },
  {
    icon: 'fa-handshake',
    title: 'Friendship',
    description: 'We build genuine, healthy, and meaningful relationships. The bonds of friendship formed through The Boys\' Brigade continue to grow and deepen within BBF.',
    official: true,
  },
  {
    icon: 'fa-circle-nodes',
    title: 'Unity',
    description: 'Members may come from different companies, churches, councils, and locations — yet one shared Brigade identity provides the foundation for cooperation and brotherhood.',
    official: false,
  },
  {
    icon: 'fa-medal',
    title: 'Discipline',
    description: 'The discipline of The Boys\' Brigade shapes our conduct — order, respect, punctuality, and commitment to duty remain central to our character as members.',
    official: false,
  },
  {
    icon: 'fa-scale-balanced',
    title: 'Integrity',
    description: 'Members shall conduct themselves with honesty, transparency, and accountability — in the Fellowship, in The Boys\' Brigade, and in society.',
    official: false,
  },
  {
    icon: 'fa-person-chalkboard',
    title: 'Personal Development',
    description: 'BBF encourages every member to develop their talents, grow professionally, and advance their Brigade qualifications — beginning with the Brigade School Certificate.',
    official: false,
  },
];

// ─── WHY JOIN BBF (BENEFITS) ──────────────────────────────────────────────────
// Source: BBF Constitution, Articles 4.2, 5, 15, 19

const BBF_BENEFITS = [
  {
    icon: 'fa-handshake',
    title: 'Genuine Brotherhood',
    description: 'Connect with fellow Boys\' Brigade members who share your values, your history, and your commitment to the Brigade spirit.',
  },
  {
    icon: 'fa-people-roof',
    title: 'Fellowship & Support',
    description: 'A fellowship that prays together, celebrates together, and supports one another during significant occasions and challenges.',
  },
  {
    icon: 'fa-hands-helping',
    title: 'Community Service',
    description: 'Participate in organised outreach, church support, community development, and service activities alongside fellow brigade members.',
  },
  {
    icon: 'fa-person-chalkboard',
    title: 'Personal Development',
    description: 'Access educational programmes, skills development, mentorship, and opportunities to grow your talents and Brigade qualifications.',
  },
  {
    icon: 'fa-network-wired',
    title: 'Wider Brigade Network',
    description: 'Build relationships across companies, churches, councils, and locations — expanding your brigade network beyond your local chapter.',
  },
  {
    icon: 'fa-calendar-check',
    title: 'Fellowship Activities',
    description: 'Attend social events, fellowship gatherings, sports, talent programmes, and Brigade-support activities organised by BBF.',
  },
  {
    icon: 'fa-heart-pulse',
    title: 'Welfare & Mutual Care',
    description: 'BBF encourages members to support each other through difficulties — welfare initiatives are a core part of the fellowship.',
  },
  {
    icon: 'fa-cross',
    title: 'Christian Community',
    description: 'Stay rooted in Christian faith through worship, prayer, Bible engagement, and shared Christian values that underpin everything BBF does.',
  },
];

// ─── LEADERSHIP ───────────────────────────────────────────────────────────────
// Source: BBF Constitution, Articles 10, 11, 12
// Official structure: Coordinator/President, Vice Coordinator/VP, Secretary,
// Asst Secretary, Financial Secretary, Treasurer, Welfare Officer,
// Social/Event Coordinator, Publicity/Media Officer.
// TODO [BACKEND]: Replace with GET /api/leaders

const BBF_LEADERS = [
  {
    id: 1,
    name: 'To Be Announced',
    title: 'Coordinator / President',
    bio: 'The Coordinator/President provides overall leadership, presides over general meetings, coordinates the activities of the Executive, and ensures the Constitution is respected.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'coordinator@bbfellowship.org',
    featured: true,
    responsibilities: ['Overall leadership', 'Presides over general meetings', 'Coordinates Executive activities', 'Represents BBF where authorised', 'Promotes unity and peaceful dispute resolution'],
  },
  {
    id: 2,
    name: 'To Be Announced',
    title: 'Vice Coordinator / Vice President',
    bio: 'The Vice Coordinator/Vice President assists the Coordinator/President, performs their duties when delegated, and supports the coordination of all fellowship activities.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'vicecoordinator@bbfellowship.org',
    featured: true,
    responsibilities: ['Assists the Coordinator/President', 'Acts when Coordinator is unavailable', 'Supports Fellowship coordination'],
  },
  {
    id: 3,
    name: 'To Be Announced',
    title: 'Secretary',
    bio: 'The Secretary keeps official minutes and records, issues meeting notices, maintains membership records, and handles all official correspondence of the Fellowship.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'secretary@bbfellowship.org',
    featured: true,
    responsibilities: ['Official minutes and records', 'Membership records', 'Meeting notices', 'Official correspondence', 'Constitutional documents'],
  },
  {
    id: 4,
    name: 'To Be Announced',
    title: 'Assistant Secretary',
    bio: 'The Assistant Secretary supports the Secretary and performs the Secretary\'s duties when delegated or when the Secretary is unavailable.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'asst.secretary@bbfellowship.org',
    featured: false,
    responsibilities: ['Supports the Secretary', 'Performs Secretary duties when delegated'],
  },
  {
    id: 5,
    name: 'To Be Announced',
    title: 'Financial Secretary',
    bio: 'The Financial Secretary records all money received, maintains accurate financial records, and works with the Treasurer to ensure full financial accountability.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'financialsecretary@bbfellowship.org',
    featured: true,
    responsibilities: ['Records all money received', 'Maintains financial records', 'Issues payment acknowledgements', 'Works with Treasurer on accountability'],
  },
  {
    id: 6,
    name: 'To Be Announced',
    title: 'Treasurer',
    bio: 'The Treasurer safeguards Fellowship funds, maintains records of funds received and disbursed, makes authorised payments, and ensures funds are used only for approved purposes.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'treasurer@bbfellowship.org',
    featured: true,
    responsibilities: ['Safeguards Fellowship funds', 'Records income and disbursements', 'Makes authorised payments', 'Presents financial statements'],
  },
  {
    id: 7,
    name: 'To Be Announced',
    title: 'Welfare Officer',
    bio: 'The Welfare Officer coordinates welfare initiatives, identifies members requiring support, coordinates approved assistance, and promotes care and solidarity among members.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'welfare@bbfellowship.org',
    featured: false,
    responsibilities: ['Coordinates welfare initiatives', 'Identifies members needing support', 'Promotes care and solidarity'],
  },
  {
    id: 8,
    name: 'To Be Announced',
    title: 'Social / Event Coordinator',
    bio: 'The Social/Event Coordinator plans and coordinates approved social and fellowship activities, promotes member participation, and ensures activities align with BBF\'s objectives.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'events@bbfellowship.org',
    featured: false,
    responsibilities: ['Coordinates social and fellowship activities', 'Event planning and logistics', 'Promotes member participation'],
  },
  {
    id: 9,
    name: 'To Be Announced',
    title: 'Publicity / Media Officer',
    bio: 'The Publicity/Media Officer manages authorised BBF publicity, coordinates official media content, promotes approved programmes, and protects the reputation of the Fellowship.',
    photo: 'assets/images/placeholder-leader.jpg',
    email: 'media@bbfellowship.org',
    featured: false,
    responsibilities: ['Manages authorised BBF publicity', 'Official media content', 'Promotes approved programmes', 'Protects BBF reputation'],
  },
];

// ─── CHAPTERS / LOCATIONS ─────────────────────────────────────────────────────
// Source: BBF Constitution, Article 5.3
// BBF brings together members from different companies, churches, councils,
// dioceses, states, and locations.
// NOTE: The data below is SAMPLE/PLACEHOLDER — to be replaced with real chapter data.
// TODO [BACKEND]: Replace with GET /api/chapters

const BBF_CHAPTERS = [
  // Sample structure — replace with real chapter data when available
  { id: 1,  name: 'Founding Chapter',  location: 'To be confirmed', coordinator: 'To Be Announced', members: 0, status: 'active' },
];

// ─── BBF_STATES kept for compatibility with existing render functions ──────────
// If BBF grows to have state-level structures, populate this properly.
// TODO [BACKEND]: Replace with GET /api/chapters?group=state
const BBF_STATES = BBF_CHAPTERS;

// ─── EVENTS ───────────────────────────────────────────────────────────────────
// Source: BBF Constitution, Article 19 (types of activities BBF may organise)
// NOTE: All events below are SAMPLE/PLACEHOLDER data.
// TODO [BACKEND]: Replace with GET /api/events

const BBF_EVENTS = [
  {
    id: 1,
    title: 'BBF Inaugural Fellowship Gathering',
    category: 'Fellowship',
    date: '2026-10-01',
    endDate: '2026-10-01',
    location: 'To Be Confirmed',
    description: 'The inaugural gathering of BBF members — a time of worship, fellowship, introductions, and shared commitment to the purpose of the Fellowship.',
    image: 'assets/images/placeholder-event.jpg',
    featured: true,
    status: 'upcoming',
    registrationLink: 'membership.html',
  },
  {
    id: 2,
    title: 'BBF Constitution Adoption Ceremony',
    category: 'Fellowship',
    date: '2026-10-01',
    endDate: '2026-10-01',
    location: 'To Be Confirmed',
    description: 'Formal adoption of the BBF Constitution and inauguration of the founding executive officers of the Fellowship.',
    image: 'assets/images/placeholder-event.jpg',
    featured: true,
    status: 'upcoming',
    registrationLink: 'membership.html',
  },
  {
    id: 3,
    title: 'Fellowship Hangout & Social',
    category: 'Social',
    date: '2026-11-01',
    endDate: '2026-11-01',
    location: 'To Be Confirmed',
    description: 'A relaxed social gathering for members to connect, share experiences, build friendships, and enjoy wholesome recreation together.',
    image: 'assets/images/placeholder-event.jpg',
    featured: false,
    status: 'upcoming',
    registrationLink: 'membership.html',
  },
  {
    id: 4,
    title: 'Community Service Day',
    category: 'Community Service',
    date: '2026-11-15',
    endDate: '2026-11-15',
    location: 'To Be Confirmed',
    description: 'BBF members come together to serve their local community — demonstrating the brigade spirit of service in action.',
    image: 'assets/images/placeholder-event.jpg',
    featured: false,
    status: 'upcoming',
    registrationLink: 'membership.html',
  },
  {
    id: 5,
    title: 'Personal Development Workshop',
    category: 'Training',
    date: '2026-12-06',
    endDate: '2026-12-06',
    location: 'To Be Confirmed',
    description: 'A skills and personal development session for members — covering professional growth, Brigade advancement, and life skills.',
    image: 'assets/images/placeholder-event.jpg',
    featured: false,
    status: 'upcoming',
    registrationLink: 'membership.html',
  },
];

// ─── NEWS & ANNOUNCEMENTS ─────────────────────────────────────────────────────
// NOTE: SAMPLE/PLACEHOLDER data — replace with real content.
// TODO [BACKEND]: Replace with GET /api/news

const BBF_NEWS = [
  {
    id: 1,
    title: 'Brigade Brotherhood Fellowship is Now Open for Membership',
    category: 'Announcement',
    date: '2026-09-01',
    author: 'BBF Secretariat',
    summary: 'BBF officially opens membership registration. All current and former Boys\' Brigade members who hold a Brigade School Certificate are welcome to register.',
    content: 'Full article content goes here...',
    image: 'assets/images/placeholder-news.jpg',
    featured: true,
  },
  {
    id: 2,
    title: 'BBF Constitution Formally Adopted',
    category: 'Announcement',
    date: '2026-09-01',
    author: 'BBF Secretariat',
    summary: 'The founding members of Brigade Brotherhood Fellowship formally adopted the BBF Constitution, establishing the governance framework for the Fellowship.',
    content: 'Full article content goes here...',
    image: 'assets/images/placeholder-news.jpg',
    featured: true,
  },
  {
    id: 3,
    title: 'Inaugural Fellowship Gathering Announced',
    category: 'Announcement',
    date: '2026-09-05',
    author: 'BBF Events Team',
    summary: 'The BBF inaugural fellowship gathering has been announced. Members are encouraged to register and invite fellow Boys\' Brigade members to attend.',
    content: 'Full article content goes here...',
    image: 'assets/images/placeholder-news.jpg',
    featured: false,
  },
  {
    id: 4,
    title: 'What is the Brigade School Certificate Requirement?',
    category: 'News',
    date: '2026-09-08',
    author: 'BBF Secretariat',
    summary: 'BBF explains the Brigade School Certificate membership requirement — what it means, who qualifies, and the transition arrangement for existing members.',
    content: 'Full article content goes here...',
    image: 'assets/images/placeholder-news.jpg',
    featured: false,
  },
  {
    id: 5,
    title: 'BBF Code of Conduct — What Every Member Needs to Know',
    category: 'News',
    date: '2026-09-10',
    author: 'BBF Secretariat',
    summary: 'A summary of the BBF Code of Conduct for members — covering respectful behaviour, confidentiality, Christian conduct, and protection of Fellowship unity.',
    content: 'Full article content goes here...',
    image: 'assets/images/placeholder-news.jpg',
    featured: false,
  },
  {
    id: 6,
    title: 'Executive Officers Wanted — Nominations Now Open',
    category: 'Announcement',
    date: '2026-09-12',
    author: 'BBF Secretariat',
    summary: 'Nominations are now open for all executive officer positions of BBF. Eligible members who meet the criteria in Article 12 are encouraged to indicate interest.',
    content: 'Full article content goes here...',
    image: 'assets/images/placeholder-news.jpg',
    featured: false,
  },
];

// ─── GALLERY ──────────────────────────────────────────────────────────────────
// NOTE: SAMPLE/PLACEHOLDER data — replace with real photos when available.
// TODO [BACKEND]: Replace with GET /api/gallery

const BBF_GALLERY = [
  { id: 1,  title: 'Fellowship Gathering',                  category: 'Fellowship',       image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 2,  title: 'Brigade Parade',                        category: 'Parade',           image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 3,  title: 'Personal Development Workshop',         category: 'Training',         image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 4,  title: 'Community Service Day',                 category: 'Community Service',image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 5,  title: 'BBF Social Event',                      category: 'Social',           image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 6,  title: 'Executive Officers Inauguration',       category: 'Fellowship',       image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 7,  title: 'Outreach Activity',                     category: 'Outreach',         image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 8,  title: 'Youth Members at BBF Event',            category: 'Fellowship',       image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 9,  title: 'Skills Development Session',            category: 'Training',         image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 10, title: 'Thanksgiving Service',                  category: 'Fellowship',       image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 11, title: 'Sports & Recreation Day',               category: 'Social',           image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
  { id: 12, title: 'Members in BBF Fellowship',             category: 'Fellowship',       image: 'assets/images/placeholder-gallery.jpg', year: 2026 },
];

// ─── ACTIVITIES & PROJECTS ────────────────────────────────────────────────────
// Source: BBF Constitution, Articles 19, 20
// TODO [BACKEND]: Replace with GET /api/activities

const BBF_ACTIVITIES = [
  {
    id: 1,
    icon: 'fa-people-roof',
    title: 'Fellowship Gatherings',
    description: 'Regular fellowship gatherings where members come together for worship, sharing, prayer, and mutual encouragement — the heart of BBF.',
    category: 'Fellowship',
  },
  {
    id: 2,
    icon: 'fa-hand-holding-heart',
    title: 'Community Service',
    description: 'Demonstrating the brigade spirit through meaningful contributions to churches, local communities, schools, and charitable causes.',
    category: 'Service',
  },
  {
    id: 3,
    icon: 'fa-person-chalkboard',
    title: 'Personal Development Programmes',
    description: 'Educational workshops, skills development sessions, and Brigade advancement programmes to help members grow personally and professionally.',
    category: 'Development',
  },
  {
    id: 4,
    icon: 'fa-music',
    title: 'Music & Talent Development',
    description: 'Programmes that discover, develop, and celebrate the musical and creative talents of BBF members for God\'s glory.',
    category: 'Arts',
  },
  {
    id: 5,
    icon: 'fa-futbol',
    title: 'Sports & Recreation',
    description: 'Organised sports and recreational activities that promote teamwork, healthy living, and positive social interaction among members.',
    category: 'Sports',
  },
  {
    id: 6,
    icon: 'fa-heart-pulse',
    title: 'Welfare Initiatives',
    description: 'Supporting members through significant occasions, challenges, and legitimate needs — ensuring no member stands alone.',
    category: 'Welfare',
  },
  {
    id: 7,
    icon: 'fa-network-wired',
    title: 'Networking Activities',
    description: 'Creating meaningful connections between members across different companies, churches, councils, and locations.',
    category: 'Networking',
  },
  {
    id: 8,
    icon: 'fa-cross',
    title: 'Christian Programmes',
    description: 'Worship, Bible study, evangelism support, and Christian fellowship activities rooted in the Christian foundation of BBF.',
    category: 'Faith',
  },
];

// ─── MEMBERSHIP ───────────────────────────────────────────────────────────────
// Source: BBF Constitution, Articles 7, 5.2
// Eligibility: active/former Boys' Brigade members + Brigade School Certificate required
// TODO [BACKEND]: Membership fees/tiers to be set by the Fellowship — these are placeholders.

const BBF_MEMBERSHIP_TYPES = [
  {
    id: 'member',
    title: 'BBF Member',
    price: 'TBC',
    period: 'per year',
    description: 'For active and former Boys\' Brigade members who hold or are working towards the Brigade School Certificate.',
    features: [
      'Participation in all Fellowship activities',
      'Voting rights (where eligible)',
      'Access to welfare support',
      'Fellowship newsletter & resources',
      'Eligibility to stand for office (Full Members)',
    ],
    highlight: true,
    requirements: [
      'Active or former Boys\' Brigade member',
      'Brigade School Certificate (or in progress)',
      'Agreement to abide by the BBF Constitution',
      'Submission of membership registration',
    ],
  },
];

// ─── MEMBERSHIP REQUIREMENTS ──────────────────────────────────────────────────
// Source: BBF Constitution, Articles 7, 5.2, 12.2

const BBF_MEMBERSHIP_REQUIREMENTS = [
  {
    icon: 'fa-shield-halved',
    title: 'Boys\' Brigade Member',
    desc: 'Active or former member of The Boys\' Brigade.',
  },
  {
    icon: 'fa-graduation-cap',
    title: 'Brigade School Certificate',
    desc: 'Minimum qualification for new members. Existing members without this may remain during the transition period.',
  },
  {
    icon: 'fa-file-signature',
    title: 'Registration',
    desc: 'Submission of a completed membership registration through the approved process.',
  },
  {
    icon: 'fa-book-open',
    title: 'Constitutional Agreement',
    desc: 'Agreement to abide by the BBF Constitution, Code of Conduct, and values of the Fellowship.',
  },
];

// ─── CONSTITUTION ARTICLES SUMMARY ───────────────────────────────────────────
// Quick reference for the Resources page
// Source: BBF Constitution

const BBF_CONSTITUTION_ARTICLES = [
  { num: 1,  title: 'Name, Identity and Motto' },
  { num: 2,  title: 'Christian Foundation' },
  { num: 3,  title: 'Logo and Symbolism' },
  { num: 4,  title: 'Purpose, Aims and Objectives' },
  { num: 5,  title: 'Purpose and Scope of the Fellowship' },
  { num: 6,  title: 'Relationship with The Boys\' Brigade' },
  { num: 7,  title: 'Membership' },
  { num: 8,  title: 'Rights and Responsibilities of Members' },
  { num: 9,  title: 'Code of Conduct' },
  { num: 10, title: 'Leadership Structure' },
  { num: 11, title: 'Duties of Officers' },
  { num: 12, title: 'Elections and Tenure of Office' },
  { num: 13, title: 'Meetings' },
  { num: 14, title: 'Finance' },
  { num: 15, title: 'Welfare and Mutual Support' },
  { num: 16, title: 'Discipline and Dispute Resolution' },
  { num: 17, title: 'Conflict of Interest' },
  { num: 18, title: 'Official Communication and Media' },
  { num: 19, title: 'Activities and Programmes' },
  { num: 20, title: 'Community Service' },
  { num: 21, title: 'Constitutional Amendments' },
  { num: 22, title: 'Interpretation of the Constitution' },
  { num: 23, title: 'Dissolution' },
  { num: 24, title: 'General Provisions' },
  { num: 25, title: 'Adoption and Commencement' },
  { num: 26, title: 'Closing Declaration' },
];

// ─── LOGO SYMBOLISM ───────────────────────────────────────────────────────────
// Source: BBF Constitution, Article 3
// Used on the About page logo section

const BBF_LOGO_SYMBOLISM = [
  { icon: 'fa-anchor',        element: "The Boys' Brigade Emblem", meaning: "Represents the common bond — Sure and Steadfast. The anchor and cross remind members that BBF's identity remains rooted in The Boys' Brigade and Christ." },
  { icon: 'fa-b',             element: 'First "B" — Brigade',      meaning: 'Represents the common Brigade identity all members share, regardless of company, church, council, or location.' },
  { icon: 'fa-people-group',  element: 'Second "B" — Brotherhood', meaning: 'Represents unity, friendship, mutual support, trust, cooperation, and commitment to one another as members of one wider Brigade family.' },
  { icon: 'fa-circle-nodes',  element: '"F" — Fellowship',         meaning: 'Represents the platform through which members interact, socialise, learn, support one another, develop talents, and build meaningful relationships.' },
  { icon: 'fa-minus',         element: 'The Gold Curved Line',     meaning: 'Represents connection, unity, progress, and shared purpose — demonstrating that Brigade, Brotherhood, and Fellowship work together.' },
  { icon: 'fa-users',         element: 'The Four People',          meaning: 'Members standing together — symbolising unity, friendship, teamwork, community, and mutual support. No member stands alone.' },
  { icon: 'fa-circle',        element: 'The Circle',               meaning: 'Represents unity, continuity, belonging, and equality of membership — every member is part of the same Fellowship circle.' },
];

// ─── COLOUR SYMBOLISM ─────────────────────────────────────────────────────────
// Source: BBF Constitution, Article 3.8

const BBF_COLOURS = [
  { name: 'Navy Blue', hex: '#0A1628', meaning: 'Discipline · Loyalty · Stability · Responsibility · Professionalism' },
  { name: 'Gold',      hex: '#C9A84C', meaning: 'Excellence · Value · Achievement · Leadership · The importance of every member' },
  { name: 'White',     hex: '#FFFFFF', meaning: 'Purity · Peace · Integrity · Transparency' },
  { name: 'Red',       hex: '#B22234', meaning: 'Sacrifice · Courage · Commitment · Christian service (used within the Christian emblem)' },
];

// ─── NIGERIAN STATES (for forms) ─────────────────────────────────────────────

const NIGERIA_STATES = [
  'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno',
  'Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT Abuja','Gombe',
  'Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos',
  'Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto',
  'Taraba','Yobe','Zamfara',
];

// ─── MOCK MEMBER (Dashboard) ──────────────────────────────────────────────────
// TODO [BACKEND]: Replace with GET /api/member/profile (authenticated)

const BBF_MOCK_MEMBER = {
  id: 'BBF-0001',
  name: 'John Adeyemi',
  email: 'john.adeyemi@email.com',
  phone: '+234 803 456 7890',
  location: 'Lagos',
  company: '1st Lagos Company, The Boys\' Brigade',
  council: 'Lagos Council',
  brigadeSchoolCert: true,
  memberSince: '2026-09-01',
  membershipStatus: 'Active',
  profilePhoto: 'assets/images/placeholder-avatar.jpg',
  notifications: [
    { id: 1, message: 'Welcome to Brigade Brotherhood Fellowship!', date: '2026-09-01', read: false },
    { id: 2, message: 'The inaugural fellowship gathering date has been announced.', date: '2026-09-05', read: false },
    { id: 3, message: 'Your membership registration has been confirmed.', date: '2026-09-01', read: true },
  ],
};

// ─── EXPORT (for module-aware environments) ───────────────────────────────────
// In a future ES-module or bundled build, export these.
// For now, all variables are global since this is loaded as a plain <script>.

// TODO [BACKEND MIGRATION]: When switching to modules/API, export like:
// export { BBF_INFO, BBF_STATS, BBF_VALUES, BBF_BENEFITS, BBF_LEADERS,
//          BBF_CHAPTERS, BBF_STATES, BBF_EVENTS, BBF_NEWS, BBF_GALLERY,
//          BBF_ACTIVITIES, BBF_MEMBERSHIP_TYPES, BBF_MEMBERSHIP_REQUIREMENTS,
//          BBF_CONSTITUTION_ARTICLES, BBF_LOGO_SYMBOLISM, BBF_COLOURS,
//          BBF_MOCK_MEMBER, NIGERIA_STATES };
