<option data-is-available="{{ $version->isAvailable() ? '1' : '0' }}" value="{{ $version->getHash() }}">{{ $version->getCommitMessage() }} ({{ $version->getHash(7) }})</option>