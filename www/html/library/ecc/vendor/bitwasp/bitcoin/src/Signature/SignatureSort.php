<?php

namespace BitWasp\Bitcoin\Signature;

use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Buffertools\BufferInterface;

class SignatureSort implements SignatureSortInterface
{
    /**
     * @var EcAdapterInterface
     */
    private $ecAdapter;

    /**
     * SignatureSort constructor.
     * @param EcAdapterInterface $ecAdapter
     */
    public function __construct(EcAdapterInterface $ecAdapter)
    {
        $this->ecAdapter = $ecAdapter;
    }

    /**
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Signature\SignatureInterface[] $signatures
     * @param \BitWasp\Bitcoin\Crypto\EcAdapter\Key\PublicKeyInterface[] $publicKeys
     * @param BufferInterface $messageHash
     * @return \SplObjectStorage
     */
    public function link(array $signatures, array $publicKeys, BufferInterface $messageHash)
    {
        $sigCount = count($signatures);
        $storage = new \SplObjectStorage();
        foreach ($signatures as $signature) {
            foreach ($publicKeys as $key) {
                if ($this->ecAdapter->verify($messageHash, $key, $signature)) {
                    $storage->attach($key, $signature);
                    if (count($storage) === $sigCount) {
                        break 2;
                    }

                    break;
                }
            }
        }

        return $storage;
    }
}
