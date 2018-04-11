<?php

/*
 * @copyright C UAB NFQ Technologies
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\Registry;

/**
 * Class TaskType.
 */
class TaskType extends AbstractType
{
    /**
     * @var Registry
     */
    private $workflows;

    /**
     * TaskType constructor.
     *
     * @param Registry $workflows
     */
    public function __construct(Registry $workflows)
    {
        $this->workflows = $workflows;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Task $data */
                $data = $event->getData() ?? new Task();
                $form = $event->getForm();
                $status = $data->getStatus();

                $form
                    ->add('title', TextType::class, ['disabled' => $status !== Task::STATUS_NEW])
                    ->add(
                        'timeSpent',
                        IntegerType::class,
                        [
                            'required' => $status === Task::STATUS_IN_PROGRESS,
                            'disabled' => $status !== Task::STATUS_IN_PROGRESS,
                        ]
                    )
                    ->add(
                        'commentNeeded',
                        ChoiceType::class,
                        [
                            'required' => $status === Task::STATUS_NEW,
                            'disabled' => $status !== Task::STATUS_NEW,
                            'choices' => [
                                'sylius.ui.yes_label' => true,
                                'sylius.ui.no_label' => false,
                            ],
                            'placeholder' => '',
                        ]
                    )
                    ->add(
                        'comment',
                        TextareaType::class,
                        [
                            'required' => $status === Task::STATUS_WAITING_FOR_COMMENT,
                            'disabled' => $status !== Task::STATUS_WAITING_FOR_COMMENT,
                        ]
                    );

                $workflow = $this->workflows->get($data);
                foreach ($workflow->getEnabledTransitions($data) as $transition) {
                    if ($transition->getName() === Task::TRANSITION_CREATE) {
                        continue;
                    }

                    $name = 'transition_' . $transition->getName();
                    $form->add($name, SubmitType::class, ['label' => $transition->getName()]);
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var Form $form */
                $form = $event->getForm();
                if (false === $form->isValid()) {
                    return;
                }

                /** @var Task $data */
                $data = $event->getData();

                $button = $form->getClickedButton();
                if (null === $button) {
                    if ($data->getStatus() === Task::STATUS_NEW) {
                        $this->workflows->get($data)->apply($data, Task::TRANSITION_CREATE);
                    }

                    return;
                }

                $workflow = $this->workflows->get($data);
                $transition = substr($button->getName(), 11);

                if (false === $workflow->can($data, $transition)) {
                    $button->addError(new FormError(sprintf('Could not apply transition %s', $transition)));

                    return;
                }

                $workflow->apply($data, $transition);
            },
            -255
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Task::class,
                'validation_groups' => function (FormInterface $form) {
                    /** @var Task $data */
                    $data = $form->getData() ?? new Task();

                    return ['Default', $data->getStatus()];
                },
            ]
        );
    }
}
