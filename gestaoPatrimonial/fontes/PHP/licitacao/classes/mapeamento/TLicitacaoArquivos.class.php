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
    * Classe de mapeamento da tabela licitacao.arquivos
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17482 $
    $Name$
    $Author: larocca $
    $Date: 2006-11-08 08:51:42 -0200 (Qua, 08 Nov 2006) $

    * Casos de uso: uc-03.05.10
*/
/*
$Log$
Revision 1.2  2006/11/08 10:51:41  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.arquivos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoArquivos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoArquivos()
{
    parent::Persistente();
    $this->setTabela("licitacao.arquivos");

    $this->setCampoCod('cod_arquivo');
    $this->setComplementoChave('cod_tipo_texto,sistema');

    $this->AddCampo('cod_arquivo'   ,'sequence',false ,''     ,true,false);
    $this->AddCampo('cod_tipo_texto','integer' ,false ,''     ,true,'TLicitacaoTextosModelo');
    $this->AddCampo('sistema'       ,'boolean' ,false ,''     ,true,'TLicitacaoTextosModelo');
    $this->AddCampo('padrao'        ,'boolean' ,false ,''     ,false,false);
    $this->AddCampo('localizacao'   ,'varchar' ,false ,'100'  ,false,false);

}
}
