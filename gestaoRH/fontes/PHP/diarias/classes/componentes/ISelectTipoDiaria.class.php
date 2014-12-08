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
    * Classe do componente TipoDiaria
    * Data de Criação: 06/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.09.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class ISelectTipoDiaria extends Select
{
/**
    * Método construtor
    * @access Private
*/
function ISelectTipoDiaria()
{
    parent::Select();

    include_once(CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php");
    $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
    $obTDiariasTipoDiaria->recuperaRelacionamento($rsTipoDiaria,""," ORDER BY nom_tipo");

    $this->setName                    ( "inCodTipoDiaria"                     );
    $this->setRotulo                  ( "Tipo de Diária"                      );
    $this->setTitle                   ( "Selecione o tipo de diária."         );
    $this->setCampoId                 ( "cod_tipo"                            );
    $this->setCampoDesc               ( "[nom_tipo]-[valor]"                  );
    $this->addOption                  ( "", "Selecione"                       );
    $this->setStyle                   ( "width: 500px"                        );
    $this->preencheCombo              ( $rsTipoDiaria                         );
}

}
?>
