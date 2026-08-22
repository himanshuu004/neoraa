<?php
// Section Component — Mellow-inspired wrapper
// Accepts: $section_title, $section_bg_color (legacy class kept), $section_id, $section_content
if (!isset($section_title))    $section_title    = '';
if (!isset($section_bg_color)) $section_bg_color = 'bg-white';
if (!isset($section_id))       $section_id       = '';

// Map legacy bg classes to nd- classes
$ndBgMap = [
    'bg-white'   => 'nd-bg-white',
    'bg-gray-50' => 'nd-bg-gray',
    'bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50' => 'nd-bg-gray',
];
$ndBgClass = $ndBgMap[$section_bg_color] ?? 'nd-bg-white';
?>
<section
    id="<?php echo htmlspecialchars($section_id); ?>"
    class="nd-section <?php echo $ndBgClass; ?>"
>
    <div class="nd-section-inner">
        <?php if ($section_title): ?>
            <div class="nd-heading-group nd-heading-group--center nd-heading-padded" data-aos="fade-up">
                <h2 class="nd-section-title">
                    <?php echo htmlspecialchars($section_title); ?>
                </h2>
            </div>
        <?php endif; ?>
        <?php echo $section_content ?? ''; ?>
    </div>
</section>
