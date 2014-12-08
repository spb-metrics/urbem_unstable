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
    * Classe de mapeamento da tabela ponto.importacao_ponto_erro
    * Data de Criação: 09/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.04

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoImportacaoPontoErro extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoImportacaoPontoErro()
{
    parent::Persistente();
    $this->setTabela("ponto.importacao_ponto_erro");

    $this->setCampoCod('cod_ponto_erro');
    $this->setComplementoChave('');

    $this->AddCampo('cod_ponto_erro'      ,'sequence',true  ,''     ,true  ,false);
    $this->AddCampo('cod_formato'         ,'integer' ,true  ,''     ,false ,'TPontoFormatoImportacao');
    $this->AddCampo('linha'               ,'varchar' ,true  ,'150'  ,false ,false);
    $this->AddCampo('cod_importacao_erro' ,'integer' ,true  ,''     ,false ,false);

}
}
?>
