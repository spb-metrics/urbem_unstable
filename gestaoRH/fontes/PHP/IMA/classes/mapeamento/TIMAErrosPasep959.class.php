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
    * Classe de mapeamento da tabela ima.erros_pasep_959
    * Data de Criação: 04/06/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: TIMAErrosPasep959.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.erros_pasep_959
  * Data de Criação: 04/06/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAErrosPasep959 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAErrosPasep959()
{
    parent::Persistente();
    $this->setTabela("ima.erros_pasep_959");

    $this->setCampoCod('cod_erro');
    $this->setComplementoChave('');

    $this->AddCampo('cod_erro'      ,'sequence',true  ,''     ,true,false);
    $this->AddCampo('num_ocorrencia','integer' ,true  ,''     ,false,'TIMAOcorrenciaDetalhe959');
    $this->AddCampo('registro'      ,'integer' ,true  ,''     ,false,false);
    $this->AddCampo('nome'          ,'varchar' ,true  ,'200'  ,false,false);
    $this->AddCampo('pis_pasep'     ,'varchar' ,true  ,'15'   ,false,false);

}
}
?>
