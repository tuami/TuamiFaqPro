"""Check the generated installation ZIP, never a source-code archive."""
import json
from pathlib import Path, PurePosixPath
import sys
import zipfile

archives = list(Path(sys.argv[1]).glob('*.zip'))
assert len(archives) == 1, f'Expected one installation ZIP, got {archives}'
expected = json.loads((Path(__file__).resolve().parents[1] / 'composer.json').read_text())
with zipfile.ZipFile(archives[0]) as archive:
    assert archive.testzip() is None, 'Invalid ZIP checksum'
    names = set(archive.namelist())
    root = 'TuamiFaqPro/'
    assert all(n.startswith(root) and '..' not in PurePosixPath(n).parts for n in names)
    for required in ('composer.json', 'src/TuamiFaqPro.php',
                     'src/Resources/config/services.php', 'src/Resources/config/routes.php',
                     'src/Resources/config/plugin.png',
                     'src/Resources/public/administration/.vite/entrypoints.json',
                     'src/Resources/public/administration/.vite/manifest.json'):
        assert root + required in names, f'Missing {required}'
    metadata = json.loads(archive.read(root + 'composer.json'))
    assert metadata['version'] == expected['version'], 'Unexpected plugin version'
    assert metadata['extra']['shopware-plugin-class'] == expected['extra']['shopware-plugin-class']
    assert any('/administration/' in n and n.endswith('.js') for n in names), 'Missing admin build'
    for n in names:
        relative = n[len(root):]
        assert not relative.startswith(('tests/', 'tools/', '.git/', '.github/', '.agents/', '.codex/')), n
        assert not relative.endswith('.zip'), f'Nested ZIP: {n}'
    for obsolete in ('src/Resources/config/services.xml', 'src/Resources/config/routes.xml',
                     'src/Resources/snippet/de-DE.json', 'src/Resources/snippet/en-GB.json',
                     'src/Resources/public/storefront/tuami-faq-pro.css'):
        assert root + obsolete not in names, f'Obsolete file: {obsolete}'
print(f'PASS: {archives[0].name}, plugin {expected["version"]}, {len(names)} entries')
