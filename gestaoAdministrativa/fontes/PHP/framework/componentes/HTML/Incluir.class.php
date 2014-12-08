<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Gerar o componente tipo Button Incluir para lista
* Data de Criação: 08/02/2004

* @author Desenvolvedor: Diego Barbosa Victoria
          Desenvolvedor: Andre Almeida

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gerar o componente tipo text que formate seu valor como data
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria
*/
class Incluir extends Button
{
/**
    * Método Construtor
    * @access Public
    * @param Boolean $boBlock
*/
function Incluir()
{
    parent::Button();
    $this->setName      ( "btIncluir" );
    $this->setValue     ( "Incluir" );
    $this->setStyle     ( "width: 60px" );
    $this->setDefinicao ( "INCLUIR" );
}

}
?>
