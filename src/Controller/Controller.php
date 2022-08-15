<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use InvalidArgumentException;

class Controller extends AbstractController
{
    /**
     * @return ?User
     */
    protected function getUser(): ?UserInterface
    {
        return parent::getUser();
    }

    protected function transformUnwantedChars(string $string): string
    {
        $unwantedArray = ['Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 'ş'=>'s', 'ü'=>'u', 'Š'=>'S', 'š'=>'s',
            'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O',
            'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
            'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n',
            'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y',
            'þ'=>'b', 'ÿ'=>'y'];

        return strtr(strip_tags($string), $unwantedArray);
    }

    protected function checkImage(FormInterface $form): bool
    {
        if (!is_null(($form->get('image')->getData())) &&
            !str_starts_with($form->get('image')->getData()->getMimeType(), 'image/')) {
            $form->addError(new FormError('Het bestand is geen plaatje.'));
            return false;
        }

        return true;
    }

    protected function moveImage(object $entity, ?UploadedFile $image)
    {
        if (!is_null($image)) {
            $entity->moveImage($image, $this->getParameter('kernel.environment'),
                $this->getParameter('kernel.project_dir'));
            $extension = $image->getClientOriginalExtension();
            $path = $this->getParameter('kernel.project_dir') . '/public/' .
                $entity->getImagePath($this->getParameter('kernel.environment'));
            if ($extension === 'png') {
                $image = imagecreatefrompng($path);
            } elseif ($extension === 'jpg' || $extension === 'jpeg') {
                $image = imagecreatefromjpeg($path);
            } elseif ($extension === 'gif') {
                $image = imagecreatefromgif($path);
            } elseif ($extension === 'bmp') {
                $image = imagecreatefrombmp($path);
            } elseif ($extension === 'wbmp') {
                $image = imagecreatefromwbmp($path);
            } elseif ($extension === 'webp') {
                $image = imagecreatefromwebp($path);
            } else {
                throw new InvalidArgumentException('Uploaded file is not an image.');
            }
            $x = imagesx($image);
            $y = imagesy($image);
            foreach ($entity::IMAGE_WIDTHS as $width) {
                $dst = imagecreatetruecolor($width, (int)($y * $width / $x));
                imagecopyresampled($dst, $image, 0, 0, 0, 0,
                    $width, (int)($y * $width / $x), $x, $y);
                $path = $this->getParameter('kernel.project_dir') . '/public/' .
                    $entity->getImagePath($this->getParameter('kernel.environment'), $width);
                if ($extension === 'png') {
                    imagepng($dst, $path);
                } elseif ($extension === 'jpg' || $extension === 'jpeg') {
                    imagejpeg($dst, $path);
                } elseif ($extension === 'gif') {
                    imagegif($dst, $path);
                } elseif ($extension === 'bmp') {
                    imagebmp($dst, $path);
                } elseif ($extension === 'wbmp') {
                    imagewbmp($dst, $path);
                } elseif ($extension === 'webp') {
                    imagewebp($dst, $path);
                } else {
                    throw new InvalidArgumentException('Uploaded file is not an image.');
                }
                imagedestroy($dst);
            }
            imagedestroy($image);
        }
    }

    protected function checkPending(Recipe $recipe): void
    {
        if ($recipe->getPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
        }
    }
}
