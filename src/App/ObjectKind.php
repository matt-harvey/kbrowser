<?php

namespace App;

enum ObjectKind: string
{
    case DAEMON_SET = 'DaemonSet';
    case DEPLOYMENT = 'Deployment';
    case NAMESPACE = 'Namespace';
    case POD = 'Pod';
    case STATEFUL_SET = 'StatefulSet';

    public function smallTitle(): string
    {
        return \strtolower($this->value);
    }

    public function title(): string
    {
        return $this->value;
    }

    public function pluralSmallTitle(): string
    {
        return $this->smallTitle() . 's';
    }

    public function pluralTitle(): string
    {
        return $this->title() . 's';
    }
}